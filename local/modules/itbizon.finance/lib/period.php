<?php

namespace Itbizon\Finance;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Exception;
use Itbizon\Finance\Model\OperationTable;
use Itbizon\Finance\Model\PeriodTable;
use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Model\VaultTable;
use Itbizon\Service\Log;

Loc::loadMessages(__FILE__);

/**
 * Class Period
 * @package Itbizon\Finance
 */
class Period extends Model\EO_Period
{
    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return Model\PeriodTable::getStatus($this->getStatus());
    }

    /**
     * @return Model\EO_Operation_Collection|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getIncomeOperation()
    {
        return OperationTable::getList([
            'filter' => [
                '=STATUS' => OperationTable::STATUS_COMMIT,
                '=TYPE' => OperationTable::TYPE_INCOME,
                '>=DATE_COMMIT' => $this->getDateStart()->format('d.m.Y H:i:s'),
                '<=DATE_COMMIT' => $this->getDateEnd()->format('d.m.Y H:i:s'),
                '!=DST_VAULT.TYPE' => VaultTable::TYPE_STOCK
            ]
        ])->fetchCollection();
    }

    /**
     * @param int $userId
     * @param array $data
     * @return UpdateResult
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentNullException
     * @throws ObjectException
     * @throws Exception
     */
    public function distribute(int $userId, array $data)
    {
        if(!Loader::includeModule('itbizon.service'))
            throw new Exception('Error load module itbizon.service');
        $log = new Log('planning');
        $log->add('Распределение выручки: '.print_r($data, true));

        $currentDate = new DateTime();
        if($currentDate <= $this->getDateEnd())
            throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.PERIOD_NOT_END'));
        if($this->getStatus() !== PeriodTable::STATUS_DISTRIBUTION_PROCEEDS)
            throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.PERIOD_ALREADY_DISTRIBUTE'));

        $errors = Stock::check();
        if(!$errors->isEmpty()) {
            $errArray = [];
            foreach($errors->getValues() as $error) {
                $errArray[] = $error->getMessage();
            }
            throw new Exception(implode('; ', $errArray));
        }

        foreach($data as $stockId => $amount) {
            if($amount < 0) {
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.INVALID_DISTRIBUTION_AMOUNT'));
            }
            if($stockId > 0) {
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.CUSTOM_DISTRIBUTION_ALREADY'));
            }
        }

        $options = Option::getForModule('itbizon.finance');

        $distributeAmount = $data[0];
        if($distributeAmount > 0) {
            $tree = StockTable::getTree();
            $distributeData = [];
            $distributeAmountSuccess = $tree->getDistributeData($distributeAmount, $distributeData);
            foreach($distributeData as $item) {
                if($item->getAmount() > 0) {
                    $log->add('Распределили '.$item->getAmount().' в '.$item->getStock()->getName());
                    $operation = Operation::createIncome([
                        'NAME' => Loc::getMessage('ITB_FIN.PERIOD.INCOME_NAME'),
                        'AMOUNT' => $item->getAmount(),
                        'DST_VAULT_ID' => $item->getStock()->getId(),
                        'CATEGORY_ID' => $options['incomeCategoryId'],
                        'COMMENT' => str_replace(
                            ['#START#', '#END#'],
                            [$this->getDateStart()->format('d.m.Y H:i:s'), $this->getDateEnd()->format('d.m.Y H:i:s')],
                            Loc::getMessage('ITB_FIN.PERIOD.INCOME_COMMENT')),
                        'RESPONSIBLE_ID' => $userId,
                    ]);
                    $operation->confirm(0);
                }
            }
            $remainAmount = $distributeAmount - $distributeAmountSuccess;
            if($remainAmount > 0) {
                $log->add('Распределили '.$remainAmount.' остатков');
                $operation = Operation::createIncome([
                    'NAME' => Loc::getMessage('ITB_FIN.PERIOD.INCOME_REMAIN_NAME'),
                    'AMOUNT' => $remainAmount,
                    'DST_VAULT_ID' => $options['reserveStockId'],
                    'CATEGORY_ID' => $options['incomeCategoryId'],
                    'COMMENT' => str_replace(
                        ['#START#', '#END#'],
                        [$this->getDateStart()->format('d.m.Y H:i:s'), $this->getDateEnd()->format('d.m.Y H:i:s')],
                        Loc::getMessage('ITB_FIN.PERIOD.INCOME_REMAIN_COMMENT')),
                    'RESPONSIBLE_ID' => $userId,
                ]);
                $operation->confirm(0);
            }
            $log->add('Всего: '.$distributeAmount.' Распределено '.$distributeAmountSuccess.' Остатки: '.$remainAmount);
        } else {
            $log->add('Нечего распределять');
        }
        $this->setStatus(PeriodTable::STATUS_ALLOCATION_COSTS);
        return $this->save();
    }

    /**
     * @param int $userId
     * @return UpdateResult
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    public function close(int $userId)
    {
        $currentDate = new DateTime();
        if($currentDate <= $this->getDateEnd())
            throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.PERIOD_NOT_END'));
        if($this->getStatus() !== PeriodTable::STATUS_ALLOCATION_COSTS)
            throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.PERIOD_ALREADY_END'));

        $result = RequestTable::getList([
            'select' => ['ID', 'STATUS'],
            'filter' => [
                '>=DATE_CREATE' => $this->getDateStart(),
                '<=DATE_CREATE' => $this->getDateEnd(),
                '=STATUS' => RequestTable::STATUS_NEW
            ],
            'limit' => 1
        ]);
        if($result->fetchObject())
            throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.NO_PROCESS_REQUEST_EXISTS'));

        $result = RequestTable::getList([
            'select' => ['ID', 'CATEGORY.ID'],
            'filter' => [
                '>=DATE_CREATE' => $this->getDateStart(),
                '<=DATE_CREATE' => $this->getDateEnd(),
                '=CATEGORY.ID'  => '',
                '=STATUS' => RequestTable::STATUS_APPROVE
            ],
            'limit' => 1
        ]);
        if($result->fetchObject())
            throw new Exception(Loc::getMessage('ITB_FIN.PERIOD.ERROR.NO_CATEGORY_REQUEST_EXISTS'));

        $requests = RequestTable::getList([
            'filter' => [
                '>=DATE_CREATE' => $this->getDateStart(),
                '<=DATE_CREATE' => $this->getDateEnd(),
                '=STATUS' => [RequestTable::STATUS_APPROVE, RequestTable::STATUS_DECLINE]
            ],
        ])->fetchCollection();

        foreach($requests as $request) {
            if($request->getStatus() === RequestTable::STATUS_APPROVE) {
                $stockOperation = null;
                $vaultOperation = null;
                try {
                    if(Helper::isStockEnabled()) {
                        $stockOperation = $vaultOperation = Operation::createOutgo([
                            'AMOUNT' => $request->getAmount(),
                            'SRC_VAULT_ID' => $request->getStockId(),
                            'CATEGORY_ID' => $request->getCategoryId(),
                            'NAME' => $request->getName(),
                            'RESPONSIBLE_ID' => $request->getAuthorId(),
                            //'ENTITY_TYPE_ID' => $request->getEntityType(),
                            //'ENTITY_ID' => $request->getEntityId(),
                            'REQUEST_ID' => $request->getId(),
                            'COMMENT' => $request->getCommentData(),
                            'FILE_ID' => $request->getFileId(),
                        ]);
                        if(!$stockOperation)
                            throw new Exception(str_replace('#REQUEST_ID#', $request->getId(), Loc::getMessage('ITB_FIN.PERIOD.ERROR.CREATE_OPERATION_STOCK')));
                    }

                    $vaultOperation = Operation::createOutgo([
                        'AMOUNT' => $request->getAmount(),
                        'SRC_VAULT_ID' => $request->getVaultId(),
                        'CATEGORY_ID' => $request->getCategoryId(),
                        'NAME' => $request->getName(),
                        'RESPONSIBLE_ID' => $request->getAuthorId(),
                        'ENTITY_TYPE_ID' => $request->getEntityType(),
                        'ENTITY_ID' => $request->getEntityId(),
                        'REQUEST_ID' => $request->getId(),
                        'COMMENT' => $request->getCommentData(),
                        'FILE_ID' => $request->getFileId(),
                    ]);
                    if(!$vaultOperation)
                        throw new Exception(str_replace('#REQUEST_ID#', $request->getId(), Loc::getMessage('ITB_FIN.PERIOD.ERROR.CREATE_OPERATION')));

                    if(!$request->confirm()->isSuccess()) {
                        throw new Exception(str_replace('#REQUEST_ID#', $request->getId(), Loc::getMessage('ITB_FIN.PERIOD.ERROR.REQUEST_FIX')));
                    }
                } catch(Exception $e) {
                    if($stockOperation)
                        $stockOperation->delete();
                    if($vaultOperation)
                        $vaultOperation->delete();
                    throw new Exception('Error request processing: '.$request->getId());
                }
            } else if($request->getStatus() === RequestTable::STATUS_DECLINE) {
                if(!$request->confirm()->isSuccess()) {
                    throw new Exception(str_replace('#REQUEST_ID#', $request->getId(), Loc::getMessage('ITB_FIN.PERIOD.ERROR.REQUEST_FIX')));
                }
            }
        }
        $this->setStatus(PeriodTable::STATUS_CLOSED);
        return $this->save();
    }
}
