<?php

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Finance\Model\StockTable;
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
 * Class CITBFinanceStockList
 */
class CITBFinanceStockList extends Complex
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.LIST.ERROR.INCLUDE_FINANCE'));
            }

            $this->initRoute(
                [
                    'list' => '/',
                    'add' => 'add/',
                    'edit' => 'edit/#ID#/',
                    'category' => 'category/',
                    'transfer' => 'transfer/'
                ],
                'list'
            );
            $this->getRoute()->run();

            // List
            if($this->getRoute()->getAction() === 'list') {
                $rows = [];
                $tree = StockTable::getTree();
                $this->createListFromTree($tree, $rows);

                $grid = new GridHelper('itb_finance_stock_list', 'itb_finance_stock_list');
                $grid->setColumns([
                        ['id' => 'ID','name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                        ['id' => 'NAME','name' => Loc::getMessage("ITB_FIN.STOCK.LIST.FIELDS.NAME"), 'sort'=>'NAME', 'default'=>true,],
                        ['id' => 'DATE_CREATE','name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.DATE_CREATE'),'sort' => 'DATE_CREATE','default' => true],
                        ['id' => 'RESPONSIBLE', 'name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.RESPONSIBLE'), 'sort' => 'RESPONSIBLE_ID', 'default' => true],
                        ['id' => 'PERCENT', 'name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.PERCENT'), 'sort' => 'PERCENT', 'default' => true],
                        ['id' => 'BALANCE', 'name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.BALANCE'), 'sort' => 'BALANCE', 'default' => true],
                        ['id' => 'LOCK_BALANCE', 'name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.LOCK_BALANCE'), 'sort' => false, 'default' => true],
                        ['id' => 'FREE_BALANCE', 'name' => Loc::getMessage('ITB_FIN.STOCK.LIST.FIELDS.FREE_BALANCE'), 'sort' => false, 'default' => true],
                    ])
                    ->setRows($rows);

                $this->setGrid($grid);
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        // Include template
        $this->IncludeComponentTemplate();
    }

    /**
     * @param Stock $stock
     * @param array $rows
     * @throws ArgumentException
     * @throws ObjectNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    protected function createListFromTree(Stock $stock, array &$rows)
    {
        //Data
        $balance = $stock->getBalance();
        $lockBalance = $stock->getLockBalance();
        $data = [
            'ID' => (!$stock->isVirtualStock()) ? $stock->getId() : '',
            'NAME' => $stock->getName(),
            'DATE_CREATE' => (!$stock->isVirtualStock()) ? $stock->getDateCreate() : '',
            'RESPONSIBLE' => (!$stock->isVirtualStock()) ? '<a href="'.$stock->getResponsibleUrl().'">'.$stock->getResponsibleName().'</a>' : '',
            'PERCENT' => Money::formatfromBase($stock->getPercent()).' %',
            'BALANCE' => Money::formatfromBase($balance),
            'LOCK_BALANCE' => Money::formatfromBase($lockBalance),
            'FREE_BALANCE' => Money::formatfromBase($balance - $lockBalance),
        ];
        if($stock->isVirtualStock()) {
            foreach ($data as $key => $value) {
                if($key !== 'ID') {
                    $data[$key] = '<b>'.$value.'</b>';
                }
            }
            if($stock->getChildPercent() < 10000) {
                $data['NAME'] .= ' <span class="badge badge-danger">'.Loc::getMessage('ITB_FIN.STOCK.LIST.ERROR.PRC_SMALL').'</span>';
            }
            if($stock->getChildPercent() > 10000) {
                $data['NAME'] .= ' <span class="badge badge-danger">'.Loc::getMessage('ITB_FIN.STOCK.LIST.ERROR.PRC_BIG').'</span>';
            }
        }
        //Actions
        $actions = [];
        if(Permission::getInstance()->isAllowStockEdit($stock)) {
            $actions[] = [
                'text'    => Loc::getMessage('ITB_FIN.STOCK.LIST.ACTION.EDIT'),
                'default' => true,
                'onclick' => (new JsCode(
                    "BX.SidePanel.Instance.open(
                        '{$this->getRoute()->getUrl('edit', ['ID' => $stock->getId()])}', 
                        {
                            cacheable: false,
                            width: 450
                        })"))->getCode(),
            ];
        }
        if(!$stock->isVirtualStock()) {
            if(Permission::getInstance()->isAllowStockDelete($stock)) {
                $actions[] = [
                    'text'    => Loc::getMessage('ITB_FIN.STOCK.LIST.ACTION.DELETE'),
                    'onclick' => (new JsCode("deleteStock({$stock->getId()})"))->getCode(),
                ];
            }
        }
        $rows[] = [
            'data' => $data,
            'actions' => $actions
        ];
        //Child rows
        foreach($stock->getChildrenStocks() as $childrenStock) {
            $this->createListFromTree($childrenStock, $rows);
        }
        //Total row
        if(!$stock->getParentStock()) {
            $balance = $stock->getBalance(true);
            $lockBalance = $stock->getLockBalance(true);
            $rows[] = [
                'data' => [
                    'BALANCE' => Money::formatfromBase($balance),
                    'LOCK_BALANCE' => Money::formatfromBase($lockBalance),
                    'FREE_BALANCE' => Money::formatfromBase($balance - $lockBalance),
                ],
                'actions' => []
            ];
        }
    }
}
