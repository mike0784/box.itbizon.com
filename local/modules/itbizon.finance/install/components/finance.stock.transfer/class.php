<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\Web\Uri;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\StockTable;
use Itbizon\Finance\Operation;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Stock;
use Itbizon\Finance\Utils\Money;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\GridHelper;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinanceStockTransfer
 */
class CITBFinanceStockTransfer extends Complex
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.TRANSFER.ERROR.INCLUDE_FINANCE'));
            }

            $this->setRoute($this->arParams['HELPER']);

            if(!Permission::getInstance()->isAllowStockEdit())
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.TRANSFER.ERROR.ACCESS_DENY'));

            $request = Application::getInstance()->getContext()->getRequest();
            if($request->getPost('save') === 'Y') {
                $data = $request->getPost('DATA');
                $amount = Money::toBase($data['AMOUNT']);

                $stock = StockTable::getById($data['SRC_STOCK'])->fetchObject();
                if(!$stock) {
                    throw new Exception(Loc::getMessage('ITB_FIN.STOCK.TRANSFER.ERROR.STOCK_NOT_FOUND'));
                }
                if($stock->getBalance() - $stock->getLockBalance() < $amount) {
                    throw new Exception(str_replace(['#STOCK#'], [$stock->getName()], Loc::getMessage('ITB_FIN.STOCK.TRANSFER.ERROR.NO_MONEY')));
                }

                $operation = Operation::createTransfer([
                    'NAME' => 'Перевод',
                    'AMOUNT' => $amount,
                    'SRC_VAULT_ID' => $data['SRC_STOCK'],
                    'DST_VAULT_ID' => $data['DST_STOCK'],
                    'CATEGORY_ID' => $data['CATEGORY'],
                    'RESPONSIBLE_ID' => CurrentUser::get()->getId(),
                    'COMMENT' => $data['COMMENT'],
                ]);
                if(!$operation) {
                    throw new Exception(Loc::getMessage('ITB_FIN.STOCK.TRANSFER.ERROR.CREATE'));
                }

                if(!$operation->confirm(0)) {
                    throw new Exception(Loc::getMessage('ITB_FIN.STOCK.TRANSFER.ERROR.COMMIT'));
                }

                $uri = new Uri($this->getRoute()->getUrl('transfer'));
                if($this->getRoute()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        // Include template
        $this->IncludeComponentTemplate();
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getStockList()
    {
        $list = [];
        $result = StockTable::getList([
            'select' => ['ID', 'NAME'],
            'order' => ['NAME' => 'ASC']
        ]);
        while($row = $result->fetch()) {
            $list[$row['ID']] = $row['NAME'];
        }
        return $list;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getCategories()
    {
        $list = [];
        $result = OperationCategoryTable::getList([
            'select' => ['ID', 'NAME'],
            'filter' => ['ALLOW_TRANSFER' => 'Y'],
            'order' => ['NAME' => 'ASC']
        ]);
        while($row = $result->fetch()) {
            $list[$row['ID']] = $row['NAME'];
        }
        return $list;
    }
}
