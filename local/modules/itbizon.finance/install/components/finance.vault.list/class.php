<?php

use Bitrix\Main\Application;
use Bitrix\Main\Grid;
use Bitrix\Main\Grid\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Main\UI;
use Itbizon\Finance;


Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceVaultList
 */
class CITBFinanceVaultList extends CBitrixComponent
{
    protected $error;

    /**
     * @return bool|mixed|null
     */
    public function executeComponent()
    {
        try {
            CJSCore::RegisterExt(
                'landInit',
                [
                    "lang" => $this->GetPath() . '/templates/.default/script.js.php',
                ]
            );
            CJSCore::Init(["landInit"]);

            if (!Loader::includeModule('itbizon.finance'))
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_LIST.ERROR.INCLUDE_FIN'));

            $gridId = 'finance_vault_list';
            $gridOptions = new Grid\Options($gridId);
            $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
            $navParams = $gridOptions->GetNavParams();

            $users = [];
            $usersList = UserTable::getList([
                'select' => ['ID', 'NAME', 'LAST_NAME']
            ]);
            while ($user = $usersList->fetchObject())
                $users[$user->getId()] = $user->getName() . " " . $user->getLastName();

            //Pagination object for grid
            $nav = new UI\PageNavigation($gridId);
            $nav->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            //Fields for filter
            $filter = [
                [
                    'id' => 'ID',
                    'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.ID'),
                    'type' => 'number',
                    'default' => true
                ],
                [
                    'id' => 'DATE_CREATE',
                    'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.DATE_CREATE'),
                    'type' => 'date',
                    'default' => true
                ],
                [
                    'id' => 'NAME',
                    'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.NAME'),
                    'type' => 'text',
                    'default' => true
                ],
                [
                    'id' => 'TYPE',
                    'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.TYPE'),
                    'type' => 'list',
                    'items' => Finance\Model\VaultTable::getTypes(),
                    'default' => true
                ],
                [
                    'id' => 'RESPONSIBLE_ID',
                    'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.RESPONSIBLE'),
                    'type' => 'list',
                    'items' => $users,
                    'params' => [
                        'multiple' => 'Y'
                    ],
                    'default' => true,
                ],
                [
                    'id' => 'BALANCE',
                    'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.BALANCE'),
                    'type' => 'number',
                    'default' => true
                ],
            ];

            //Columns for grid
            $columns = [
                ['id' => 'ID', 'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                ['id' => 'NAME', 'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.NAME'), 'sort' => 'NAME', 'default' => true, 'shift' => true],
                ['id' => 'DATE_CREATE', 'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.DATE_CREATE'), 'sort' => 'DATE_CREATE', 'default' => true],
                ['id' => 'TYPE', 'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.TYPE'), 'sort' => 'TYPE', 'default' => true],
                ['id' => 'RESPONSIBLE', 'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.RESPONSIBLE'), 'sort' => 'RESPONSIBLE_ID', 'default' => true],
                ['id' => 'BALANCE', 'name' => Loc::getMessage('ITB_FIN.VAULT_LIST.FIELDS.BALANCE'), 'sort' => 'BALANCE', 'default' => true],
            ];

            //Converting UI filter to D7 filter
            $filterOption = new UI\Filter\Options($gridId);
            $filterData = Finance\Helper::FilterUI2D7(
                $filterOption->getFilter([]),
                [
                    'search' => ['NAME'],
                    'simple' => ['TYPE', 'RESPONSIBLE_ID'],
                    'date' => ['DATE_CREATE'],
                    'number' => ['ID' => 1, 'BALANCE' => 1]
                ]
            );

            //Group list
            $groups = [];
            $groupList = Finance\Model\VaultGroupTable::getList(['order' => ['NAME' => 'ASC']])->fetchCollection();
            foreach($groupList as $group) {
                $groups[$group->getId()] = [
                    'NAME'    => $group->getName(),
                    'COUNT'   => 0,
                    'BALANCE' => 0,
                ];
            }
            $groups[0] = [
                'NAME'    => 'Нет группы',
                'COUNT'   => 0,
                'BALANCE' => 0,
            ];


            // Настройка фильтра
            if (!Finance\Permission::getInstance()->isAllowVaultView())
                $filterData = array_merge($filterData, [
                    '=RESPONSIBLE_ID' => Finance\Permission::getInstance()->get()->getId()
                ]);

            $filterData['!=TYPE'] = [Finance\Model\VaultTable::TYPE_STOCK];

            $result = Finance\Model\VaultTable::getList([
                'filter' => $filterData,
                'order' => $sort['sort'],
            ]);

            //Data for grid
            $vaultRows = [];
            $sumBalance = 0;
            while ($vault = $result->fetchObject()) {
                //Data
                $temp = [
                    'ID' => strval($vault->getId()),
                    'DATE_CREATE' => $vault->getDateCreate()->format('d.m.Y'),
                    'NAME' => $vault->getName(),
                    'BALANCE' => Finance\Utils\Money::formatFromBase($vault->getBalance()),
                    'TYPE' => $vault->getTypeName(),
                    'RESPONSIBLE' => '<a href="' . $vault->getResponsibleUrl() . '">' . $vault->getResponsibleName() . '</a>',
                ];

                //Actions
                $actions = [];
                //Edit
                if (Finance\Permission::getInstance()->isAllowVaultView($vault)) {
                    $actions[] = [
                        'text' => Loc::getMessage('ITB_FIN.VAULT_LIST.ACTION.EDIT'),
                        'default' => true,
                        'onclick' => 'document.location.href="' . $this->makeEditLink($vault->getId()) . '";',
                    ];
                }
                //Delete
                if (Finance\Permission::getInstance()->isAllowVaultDelete($vault)) {
                    $actions[] = [
                        'text' => Loc::getMessage('ITB_FIN.VAULT_LIST.ACTION.DELETE'),
                        'default' => true,
                        'onclick' => 'removeVault("' . $this->makeRemoveLink($vault->getId()) . '");'
                    ];
                }

                //Add data
                $groupId = $vault->getGroupId();
                $groupId = isset($groups[$groupId]) ? $groupId : 0;
                if(isset($groups[$groupId])) {
                    $groups[$groupId]['BALANCE'] += $vault->getBalance();
                    $groups[$groupId]['COUNT']++;

                    $vaultRows[] = [
                        'data'      => $temp,
                        'actions'   => $actions,
                        'parent_id' => 'G'.$groupId,
                        'has_child' => false,
                        'editable'  => false,
                    ];
                } else {
                    $vaultRows[] = [
                        'data'      => $temp,
                        'actions'   => $actions,
                        'parent_id' => '',
                        'has_child' => false,
                        'editable'  => false,
                    ];
                }

                //Total
                $sumBalance += $vault->getBalance();
            }

            $groupRows = [];
            foreach($groups as $groupId => $group) {
                $actions = [];
                if(Finance\Permission::getInstance()->isAllowVaultGroupView() && $groupId) {
                    $actions[] = [
                        'text' => Loc::getMessage('ITB_FIN.VAULT_LIST.ACTION.EDIT_VAULT_GROUP'),
                        'default' => true,
                        'onclick' => 'document.location.href="' . $this->makeEditGroupLink($groupId) . '";',
                    ];
                }
                if(Finance\Permission::getInstance()->isAllowVaultGroupDelete() && $groupId) {
                    $actions[] = [
                        'text' => Loc::getMessage('ITB_FIN.VAULT_LIST.ACTION.DELETE_VAULT_GROUP'),
                        'default' => false,
                        'onclick' => 'removeVaultGroup("' . $this->makeRemoveGroupLink($groupId) . '");'
                    ];
                }
                $groupRows[] = [
                    'data'      => [
                        'ID' => 'G'.$groupId,
                        'NAME' => '<b>'.$group['NAME'].'</b>',
                        'BALANCE' => '<b>'.Finance\Utils\Money::formatFromBase($group['BALANCE']).'</b>'
                    ],
                    'actions'   => $actions,
                    'parent_id' => '',
                    'has_child' => ($group['COUNT'] > 0),
                    'editable'  => false,
                ];
            }

            //Total row
            $totalRows[] = [
                'data' => [
                    'ID'      => 'T',
                    'NAME'    => '<b>Итого</b>',
                    'BALANCE' => '<b>'.Finance\Utils\Money::formatFromBase($sumBalance).'</b>'
                ],
                'actions' => [],
                'parent_id' => '',
                'has_child' => false,
                'editable'  => false,
            ];

            if(!$this->isChildRequest($gridId)) {
                $rows = array_merge($totalRows, array_merge($groupRows, $vaultRows));
            } else {
                throw new Exception('Child request');
            }


            //All count for pagination
            $nav->setRecordCount(Finance\Model\VaultTable::getCount($filterData));

            $this->arResult = [
                'GRID_ID' => $gridId,
                'NAV' => $nav,
                'FILTER' => $filter,
                'COLUMNS' => $columns,
                'ROWS' => $rows,
            ];
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
    public function makeAddLink()
    {
        return $this->arParams['FOLDER'] . $this->arParams['TEMPLATE_ADD'];
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function makeEditLink($id)
    {
        return $this->arParams['FOLDER'] . str_replace('#ID#', $id, $this->arParams['TEMPLATE_EDIT']);
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeRemoveLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?remove_vault=' . $id;
    }

    /**
     * @return mixed
     */
    public function makeAddGroupLink()
    {
        return $this->arParams['FOLDER'] . $this->arParams['TEMPLATE_ADD_GROUP'];
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function makeEditGroupLink($id)
    {
        return $this->arParams['FOLDER'] . str_replace('#ID#', $id, $this->arParams['TEMPLATE_EDIT_GROUP']);
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeRemoveGroupLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?remove_group=' . $id;
    }

    /**
     * @param $gridId
     * @return bool
     */
    public function isChildRequest($gridId)
    {
        $request = Application::getInstance()->getContext()->getRequest();
        return $request->isAjaxRequest()
            && Context::isInternalRequest()
            && $_REQUEST['grid_id'] === $gridId
            && $_REQUEST['grid_action'] === "showpage"
            && $_REQUEST['action'] === "getChildRows";
    }
}
