<?php

use Bitrix\Im\Department;
use Bitrix\Main\AccessDeniedException;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Bitrix\Main\UserTable;
use Itbizon\Finance\Model\AccessRightTable;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\OperationTable;
use Itbizon\Finance\Model\RequestTemplateTable;
use Itbizon\Finance\Model\VaultGroupTable;
use Itbizon\Finance\Model\VaultTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinancePermissionList
 */
class CITBFinancePermissionList extends CBitrixComponent
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.ACCESS_DENIED"));
            }
            if(!$this->arParams['SEF_MODE'] == 'Y') {
                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.SEF"));
            }
            if(!CurrentUser::get()->isAdmin()) {
                throw new AccessDeniedException(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.ACCESS_DENIED"));
            }

            // Init variables
            $arDefaultUrlTemplates404 = [
                'list' => '/',
                'add' => 'add/',
                'edit' => 'edit/#ID#/',
            ];
            $arDefaultVariableAliases404 = [];
            $arComponentVariables = ['ID'];
            $arVariables = [];

            $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
                $arDefaultUrlTemplates404,
                $this->arParams['SEF_URL_TEMPLATES']
            );

            $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
                $arDefaultVariableAliases404,
                $this->arParams['VARIABLE_ALIASES']
            );

            $componentPage = CComponentEngine::ParseComponentPath(
                $this->arParams['SEF_FOLDER'],
                $arUrlTemplates,
                $arVariables
            );

            // Result
            $this->arResult['FOLDER'] = $this->arParams['SEF_FOLDER'];
            $this->arResult['URL_TEMPLATES'] = $arUrlTemplates;

            if(strlen($componentPage) <= 0) {
                $componentPage = 'list';

                CJSCore::RegisterExt(
                    'landInit',
                    [
                        "lang" => $this->GetPath() . '/templates/.default/script.php',
                    ]
                );
                CJSCore::Init(["landInit"]);

                $gridId = 'itb_finance_permission_list';
                $gridOptions = new Grid\Options($gridId);
                $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
                $order = $sort['sort'];
                $navParams = $gridOptions->GetNavParams();

                //Pagination object for grid
                $nav = new UI\PageNavigation($gridId);
                $nav->allowAllRecords(true)->setPageSize($navParams['nPageSize'])->initFromUri();

                // Columns for grid
                $columns = [
                    ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
                    ['id' => 'ENTITY_TYPE_ID', 'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.ENTITY_TYPE"), 'sort' => 'ENTITY_TYPE_ID', 'default' => true],
                    ['id' => 'ENTITY_ID', 'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.ENTITY"), 'sort' => 'ENTITY_ID', 'default' => true],
                    ['id' => 'ACTION', 'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.ACTION"), 'sort' => 'ACTION', 'default' => true],
                    ['id' => 'USER_ID', 'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.USER"), 'default' => true],
                ];

                //Fields for filter
                $filter = [
                    [
                        'id' => 'ID',
                        'name' => "ID",
                        'type' => 'number',
                        'default' => true
                    ],
                    [
                        'id' => 'ENTITY_TYPE_ID',
                        'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.ENTITY_TYPE"),
                        'type' => 'list',
                        'items' => AccessRightTable::getEntityTypes(),
                        'default' => true
                    ],
                    [
                        'id' => 'ENTITY_ID',
                        'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.ENTITY"),
                        'type' => 'number',
                        'default' => true
                    ],
                    [
                        'id' => 'ACTION',
                        'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.ACTION"),
                        'type' => 'list',
                        'items' => AccessRightTable::getActions(),
                        'default' => true
                    ],
                    [
                        'id' => 'USER',
                        'name' => Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.COL.USER"),
                        'type' => 'dest_selector',
                        'options' => [
                            'departmentEnable' => 'Y'
                        ],
                        'default' => true
                    ],
                ];

                // Converting UI filter to D7 filter
                $filterOption = new UI\Filter\Options($gridId);
                $filterData = $filterOption->getFilterLogic($filter);

                if(isset($filterData['USER'])) {
                    $matches = [];
                    if(preg_match('#(' . implode('|', ['U', 'DR']) . ')([0-9]+)#', $filterData['USER'], $matches) === 1
                        && !empty($matches[1]) && !empty($matches[2])
                    ) {
                        $symbol = ($matches[1] == 'U' ? AccessRightTable::USER : AccessRightTable::DEPARTMENT);
                        $id = $matches[2];

                        $filterData['=USER_TYPE'] = $symbol;
                        $filterData['=USER_ID'] = $id;
                    } else {
                        throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERROR.UNKNOW_USER"));
                    }

                    unset($filterData['USER']);
                }

                // Data for grid
                $rows = [];
                $objList = AccessRightTable::getList([
                    'count_total' => true,
                    'limit' => $nav->getLimit(),
                    'offset' => $nav->getOffset(),
                    'order' => $order,
                    'filter' => $filterData,
                ]);

                while ($obj = $objList->fetchObject()) {

                    $userTypeId = $obj->getUserType();
                    $userId = $obj->getUserId();
                    $entityTypeId = $obj->getEntityTypeId();
                    $entityId = $obj->getEntityId();
                    $userLink = "";
                    $userName = "";

                    if($userTypeId == AccessRightTable::USER) {
                        $userLink = "/company/personal/user/{$userId}/";
                        $objUser = UserTable::getById($userId)->fetchObject();
                        if($objUser->getName() || $objUser->getLastName()) {
                            $userName = "{$objUser->getName()} {$objUser->getLastName()}";
                        } else {
                            $userName = $objUser->getEmail();
                        }
                    } elseif($userTypeId == AccessRightTable::DEPARTMENT) {
                        $userLink = "/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$userId}";
                        $arrDep = Department::getStructure([
                            'FILTER' => [
                                'ID' => [$userId]
                            ]
                        ]);
                        if(isset($arrDep[0]) && isset($arrDep[0]['NAME']))
                            $userName = $arrDep[0]['NAME'];
                        else
                            $userName = Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.USER_NAME");
                    }

                    try {
                        if(!$entityId)
                            throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ENTITY_ALL"));

                        if($entityTypeId == AccessRightTable::ENTITY_VAULT) {
                            $objEntity = VaultTable::getById($entityId)->fetchObject();
                            if(!$objEntity)
                                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.OBJ_NOT_FOUND"));
                            $entityLink = "<a target='_blank' href='/finance/vault/edit/{$objEntity->getId()}/'>{$objEntity->getName()}</a>";
                        } elseif($entityTypeId == AccessRightTable::ENTITY_VAULT_GROUP) {
                            $objEntity = VaultGroupTable::getById($entityId)->fetchObject();
                            if(!$objEntity)
                                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.OBJ_NOT_FOUND"));
                            $entityLink = $objEntity->getName();
                        } elseif($entityTypeId == AccessRightTable::ENTITY_OPERATION) {
                            $objEntity = OperationTable::getById($entityId)->fetchObject();
                            if(!$objEntity)
                                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.OBJ_NOT_FOUND"));
                            $entityLink = "<a target='_blank' href='/finance/operation/edit/{$objEntity->getId()}/'>{$objEntity->getName()}</a>";
                        } elseif($entityTypeId == AccessRightTable::ENTITY_CATEGORY) {
                            $objEntity = OperationCategoryTable::getById($entityId)->fetchObject();
                            if(!$objEntity)
                                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.OBJ_NOT_FOUND"));
                            $entityLink = "<a target='_blank' href='/finance/category/edit/{$objEntity->getId()}/'>{$objEntity->getName()}</a>";
                        } elseif($entityTypeId == AccessRightTable::ENTITY_REQUEST_TEMPLATE) {
                            $objEntity = RequestTemplateTable::getById($entityId)->fetchObject();
                            if(!$objEntity)
                                throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.CLASS.ERRORS.OBJ_NOT_FOUND"));
                            $entityLink = "<a target='_blank' href='/finance/requesttemplate/edit/{$objEntity->getId()}/'>{$objEntity->getName()}</a>";
                        } else {
                            $entityLink = $entityId;
                        }
                    } catch (Exception $e) {
                        $entityLink = $e->getMessage();
                    }

                    //Data
                    $temp = [
                        'ID' => $obj->getId(),
                        'ACTION' => AccessRightTable::getActions($obj->getAction()),
                        'ENTITY_TYPE_ID' => AccessRightTable::getEntityTypes($entityTypeId),
                        'ENTITY_ID' => $entityLink,
                        'USER_ID' => "<a target='_blank' href='{$userLink}'>{$userName}</a>",
                    ];

                    //Actions
                    $actions = [
                        //Edit
                        [
                            'text' => Loc::getMessage('ITB_FINANCE.PERMISSION.LIST.CLASS.ACTION.EDIT'),
                            'default' => true,
                            'onclick' => $this->makeEditLink($obj->getId()),
                        ],
                        //Delete
                        [
                            'text' => Loc::getMessage('ITB_FINANCE.PERMISSION.LIST.CLASS.ACTION.DELETE'),
                            'default' => true,
                            'onclick' => 'remove("' . $this->makeRemoveLink() . '", ' . $obj->getId() . ');'
                        ]
                    ];

                    //Add data
                    $rows[] = [
                        'data' => $temp,
                        'actions' => $actions
                    ];

                }
                //All count for pagination
                $nav->setRecordCount($objList->getCount());
                $this->arResult = [
                    'GRID_ID' => $gridId,
                    'PAGE' => $componentPage,
                    'NAV' => $nav,
                    'COLUMNS' => $columns,
                    'ROWS' => $rows,
                    'FILTER' => $filter,
                    'PATH_ADD' => $this->makeAddLink(),
                ];
            }

            CComponentEngine::InitComponentVariables(
                $componentPage,
                $arComponentVariables,
                $arVariableAliases,
                $arVariables
            );

            // Modify variables
            $arVariables['ACTION'] = $componentPage;
            $this->arResult['VARIABLES'] = $arVariables;

            // Include template
            $this->IncludeComponentTemplate();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeEditLink($id)
    {
        $path = $this->arResult['FOLDER'] . str_replace('#ID#', $id, $this->arResult['URL_TEMPLATES']['edit']);
        return "BX.SidePanel.Instance.open('{$path}', {
            cacheable: false,
            width: 700,
            allowChangeHistory: false,
        });";
    }

    /**
     * @return string
     */
    protected function makeRemoveLink()
    {
        return $this->GetPath() . '/templates/.default/ajax.php';
    }

    /**
     * @return string
     */
    protected function makeAddLink()
    {
        $request = Context::getCurrent()->getRequest();
        return "{$request->getRequestedPageDirectory()}/{$this->arResult['URL_TEMPLATES']['add']}";
    }
}
