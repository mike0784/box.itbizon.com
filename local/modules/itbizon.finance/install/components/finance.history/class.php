<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Bitrix\Main\UserTable;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceHistory
 */
class CITBFinanceHistory extends CBitrixComponent
{
    /**
     * @return bool|mixed|null
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.HISTORY.ERROR.INCLUDE_FIN'));

            $gridId = 'finance_history';
            $gridOptions = new Grid\Options($gridId);
            $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
            $navParams = $gridOptions->GetNavParams();

            //Pagination object for grid
            $nav = new UI\PageNavigation($gridId);
            $nav->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            $users = [];
            $usersList = UserTable::getList([
                'select' => ['ID', 'NAME', 'LAST_NAME']
            ]);
            while ($user = $usersList->fetchObject())
                $users[$user->getId()] = $user->getName() . " " . $user->getLastName();

            $vaults = [];
            $vaultsList = Finance\Model\VaultTable::getList([
                'select' => ['ID', 'NAME']
            ]);
            while ($vault = $vaultsList->fetchObject())
                $vaults[$vault->getId()] = $vault->getName();

            //Fields for filter
            $filter = [
                [
                    'id' => 'ID',
                    'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.VAULT'),
                    'type' => 'list',
                    'items' => $vaults,
                    'params' => [
                        'multiple' => 'Y'
                    ],
                    'default' => true
                ],
                [
                    'id' => 'BALANCE',
                    'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.BALANCE'),
                    'type' => 'number',
                    'default' => true
                ],
                [
                    'id' => 'DATE_CREATE',
                    'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.DATE_CREATE'),
                    'type' => 'date',
                    'default' => true
                ],
                [
                    'id' => 'VAULT.RESPONSIBLE_ID',
                    'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.RESPONSIBLE'),
                    'type' => 'list',
                    'items' => $users,
                    'params' => [
                        'multiple' => 'Y'
                    ],
                    'default' => true,
                ],

            ];

            //Columns for grid
            $columns = [
                ['id' => 'ID', 'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                ['id' => 'NAME', 'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.VAULT'), 'sort' => 'VAULT.NAME', 'default' => true],
                ['id' => 'RESPONSIBLE', 'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.RESPONSIBLE'), 'sort' => 'VAULT.RESPONSIBLE_ID', 'default' => true],
                ['id' => 'DATE_CREATE', 'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.DATE_CREATE'), 'sort' => 'DATE_CREATE', 'default' => true],
                ['id' => 'BALANCE', 'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.BALANCE'), 'sort' => 'BALANCE', 'default' => true],
                ['id' => 'OPERATION', 'name' => Loc::getMessage('ITB_FIN.HISTORY.FIELDS.OPERATION'), 'sort' => 'OPERATION.NAME', 'default' => true],
            ];

            //Converting UI filter to D7 filter
            $filterOption = new UI\Filter\Options($gridId);
            $filterList = $filterOption->getFilter([]);
            $filterHistory = Finance\Helper::FilterUI2D7(
                $filterList,
                [
                    'search' => ['VAULT.NAME'],
                    'simple' => ['VAULT.RESPONSIBLE_ID'],
                    'date' => ['DATE_CREATE'],
                    'number' => ['BALANCE' => 100],
                ]
            );

            //Data for grid
            $rows = [];

            // Настройка фильтра
            $currentUser = CurrentUser::get();
            if (!$currentUser->isAdmin()) {
                $filterOption->setPresets([
                    'VAULT.RESPONSIBLE_ID' => $currentUser->getId()
                ]);
                $filterVault['=VAULT.RESPONSIBLE_ID'] = $currentUser->getId();
            }

            if (array_search('>=DATE_CREATE', $filterList) === false) {
                $historyBegin = (new DateTime())->modify('first day of this month')->setTime(0, 0, 0);
                $historyEnd = (clone $historyBegin)->modify('last day of this month')->setTime(23, 59, 59);

                $filterHistory['>=DATE_CREATE'] = $historyBegin->format('d.m.Y H:i:s');
                $filterHistory['<=DATE_CREATE'] = $historyEnd->format('d.m.Y H:i:s');
            }

            $history = Finance\Model\VaultHistoryTable::getList([
                'count_total' => true,
                'select' => [
                    '*',
                    'OPERATION.ID',
                    'OPERATION.NAME',
                    'VAULT.ID',
                    'VAULT.NAME',
                    'VAULT.RESPONSIBLE',
                ],
                'order' => $sort['sort'],
                'filter' => $filterHistory,
                'limit' => $nav->getLimit(),
                'offset' => $nav->getOffset()
            ]);

            while ($record = $history->fetchObject()) {
                if (!$record->getVault())
                    continue;
                //Data
                $temp = [
                    'ID' => $record->getId(),
                    'NAME' => '<a href="' . $record->getVault()->getUrl() . '">' . $record->getVault()->getName() . '</a>',
                    'DATE_CREATE' => $record->getDateCreate(),
                    'BALANCE' => Finance\Utils\Money::formatFromBase($record->getBalance()),
                    'OPERATION' => $record->getOperation() ?
                        '<a href="' . $record->getOperation()->getUrl() . '">' . $record->getOperation()->getName() . '</a>' :
                        Loc::getMessage('ITB_FIN.HISTORY.OPERATION_EMPTY'),
                    'RESPONSIBLE' => '<a href="' . $record->getVault()->getResponsibleUrl() . '">' . $record->getVault()->getResponsibleName() . '</a>',
                    'RESPONSIBLE_ID' => $record->getVault()->getResponsibleId(),
                ];

                //Add data
                $rows[] = [
                    'data' => $temp,
                    'actions' => []
                ];
            }

            //All count for pagination
            $nav->setRecordCount($history->getCount());
            $this->arResult = [
                'GRID_ID' => $gridId,
                'NAV' => $nav,
                'FILTER' => $filter,
                'COLUMNS' => $columns,
                'ROWS' => $rows,
            ];
        } catch (Exception $e) {
            echo $e->getMessage() . '<br>' . $e->getTraceAsString();
        }
        //Include template
        $this->IncludeComponentTemplate();
        return true;
    }
}
