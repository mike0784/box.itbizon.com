<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model\EO_OperationCategory_Collection;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\OperationTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceOperationReport
 */
class CITBFinanceOperationReport extends CBitrixComponent
{
    protected $error;
    protected $from;
    protected $to;
    protected $periods;
    protected $categories;
    protected $data = null;

    /**
     * @return bool|mixed|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.REPORT.ERROR.INCLUDE_FIN'));

            $this->data = [];
            $this->to = (new DateTime())->modify('last day of this month');
            $this->from = (clone $this->to)->modify('-11 month')->modify('first day of this month');

            $request = Application::getInstance()->getContext()->getRequest();
            $from = $request->getPost('from');
            $to   = $request->getPost('to');
            if(!empty($from) && !empty($to)) {
                $this->from->setTimestamp(strtotime($from));
                $this->to->setTimestamp(strtotime($to));

            }
            $this->from->setTime(0, 0, 0);
            $this->to->setTime(23, 59, 59);

            $data = [];
            $date = clone $this->from;
            while($date < $this->to) {
                $id = $date->format('m.Y');
                $this->periods[$id] = $id;
                $date->modify('+1 month');
            }

            if(!\Itbizon\Finance\Permission::getInstance()->isAllowCategoryReportShow())
                throw new Exception('Нет доступа');

            $collection = OperationCategoryTable::getList([
                'order' => ['NAME' => 'ASC']
            ])->fetchCollection();

            foreach ($collection as $category)
            {
                if($category->getAllowIncome() && $category->getAllowOutgo())
                    $this->categories['OTHER'][] = $category;
                elseif($category->getAllowIncome())
                    $this->categories['INCOME'][] = $category;
                else
                    $this->categories['OUTGO'][] = $category;
            }
            
            $operations = OperationTable::getList([
                'select' => ['*'],
                'filter' => [
                    '=STATUS' => OperationTable::STATUS_COMMIT,
                    '=TYPE' => [OperationTable::TYPE_INCOME, OperationTable::TYPE_OUTGO],
                    '>=DATE_COMMIT' => $this->from->format('d.m.Y H:i:s'),
                    '<=DATE_COMMIT' => $this->to->format('d.m.Y H:i:s'),
                ]
            ])->fetchCollection();

            foreach($operations as $operation) {
                $periodId = $operation->getDateCommit()->format('m.Y');
                $categoryId = $operation->getCategoryId();
                if(!isset($data[$categoryId][$periodId])) {
                    $data[$categoryId][$periodId] = [
                        'balance' => 0,
                        'operations' => []
                    ];
                }

                $value = ($operation->getType() === OperationTable::TYPE_OUTGO) ? -$operation->getAmount() : $operation->getAmount();
                $data[$categoryId][$periodId]['balance'] += $value;
                $data[$categoryId][$periodId]['operations'][] = $operation->getId();
            }
            $this->data = $data;

        } catch (Exception $e) {
            $this->error = $e->getMessage();
        }
        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return mixed
     */
    public function getPeriods(): ?array
    {
        return $this->periods;
    }

    /**
     * @return mixed
     */
    public function getCategories(): ?array
    {
        return $this->categories;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }
}
