<?php

use Bitrix\Main\Context;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Bitrix\Main\UserTable;
use Bitrix\Main\Engine\CurrentUser;

use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Helper;
use Itbizon\Finance\Model\RequestTemplateTable;
use Itbizon\Finance\Model\VaultTable;
use Itbizon\Finance\Utils\Money;
use Itbizon\Finance\Permission;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinancePeriodList
 */
class CITBFinanceRequestTemplateList extends CBitrixComponent
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.ERROR.INCLUDE_FINANCE'));
            }
            if(!$this->arParams['SEF_MODE'] == 'Y') {
                throw new Exception(Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.ERROR.SEF_MODE'));
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
                        "lang" => $this->GetPath() . '/templates/.default/script.js.php',
                    ]
                );
                CJSCore::Init(["landInit"]);
                
                $gridId = 'req_template_list';
                $gridOptions = new Grid\Options($gridId);
                $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
                $order = $sort['sort'];
                $navParams = $gridOptions->GetNavParams();
                
                //Pagination object for grid
                $nav = new UI\PageNavigation($gridId);
                $nav->allowAllRecords(true)->setPageSize($navParams['nPageSize'])->initFromUri();
                
                // Columns for grid
                $columns = [
                    ['id'=>'ID','name' => Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.ID'),'sort' => 'ID','default' => true],
                    ['id'=>'NAME','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.NAME"),'sort'=>'NAME','default'=>true,],
                    ['id'=>'AUTHOR','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.AUTHOR"),'sort'=>'AUTHOR_ID','default'=>true,],
                    ['id'=>'CATEGORY','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.CATEGORY"),'sort'=>'CATEGORY_ID','default'=>true,],
                    ['id'=>'AMOUNT','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.AMOUNT"),'sort'=>'AMOUNT','default'=>true,],
                    ['id'=>'ACTIVE','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.ACTIVE"),'sort'=>'ACTIVE','default'=>true,],
                    ['id'=>'COMMENT_SITUATION','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.COMMENT_SITUATION"),'sort'=>'COMMENT_SITUATION','default'=>true,],
                    ['id'=>'COMMENT_DATA','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.COMMENT_DATA"),'sort'=>'COMMENT_DATA','default'=>true,],
                    ['id'=>'COMMENT_SOLUTION','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.COMMENT_SOLUTION"),'sort'=>'COMMENT_SOLUTION','default'=>true,],
                    ['id'=>'ENTITY','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.ENTITY"),'sort'=>'ENTITY_ID','default'=>true,],
                    ['id'=>'VAULT','name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.VAULT"),'sort'=>'VAULT_ID','default'=>true,],
                ];
                
                $category = [];
                $list = OperationCategoryTable::getList([
                    'filter'=>[
                        '=ALLOW_OUTGO'=>true,
                    ],
                    'select'=>[
                        'ID',
                        'NAME',
                    ]
                ]);
                while($row = $list->fetch())
                    $category[$row['ID']] = $row['NAME'];
                asort($category);
                
                $users = [];
                $list = UserTable::getList([
                    'filter'=>[
                        'ACTIVE'=>'Y',
                    ],
                    'select'=>[
                        'ID',
                        'NAME',
                        'LAST_NAME',
                    ]
                ]);
                while($row = $list->fetch())
                    $users[$row['ID']] = $row['LAST_NAME'].' '.$row['NAME'];
                
                $vault = [];
                $list = VaultTable::getList(['select'=>['ID','NAME']]);
                while($row = $list->fetch())
                    $vault[$row['ID']] = $row['NAME'];
                
                //Fields for filter
                $filter = [
                    [
                        'id' => 'NAME',
                        'name' => Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.NAME'),
                        'type' => 'string',
                        'default' => true
                    ],
                    [
                        'id' => 'CATEGORY_ID',
                        'name' => Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.CATEGORY_ID'),
                        'type' => 'list',
                        'items'=>$category,
                        'default' => true,
                        'params'=>[
                            'multiple'=>'Y',
                        ],
                    ],
                    [
                        'id'=>'ACTIVE',
                        'name'=>Loc::getMessage("ITB_FIN.REQ_TEMPLATE.LIST.FIELDS.ACTIVE"),
                        'type'=>'list',
                        'items'=>[
                            'Y'=>RequestTemplateTable::getActiveName('Y'),
                            'N'=>RequestTemplateTable::getActiveName('N'),
                        ],
                        'default'=>true,
                    ]
                ];
                
                // Converting UI filter to D7 filter
                $filterOption = new UI\Filter\Options($gridId);
                $filterData = Helper::FilterUI2D7(
                    $filterOption->getFilter([]),
                    [
                        'search' => ['NAME'],
                        'simple' => [
                            'CATEGORY_ID','ACTIVE',
                        ],
                    ]
                );
                
                if(!CurrentUser::get()->isAdmin() && !Permission::getInstance()->isAllowRequestTemplateEdit())
                    $filterData['=AUTHOR_ID'] = CurrentUser::get()->getId();
                
                // Data for grid
                $rows = [];
                $objList = RequestTemplateTable::getList(
                    [
                        'limit' => $nav->getLimit(),
                        'offset' => $nav->getOffset(),
                        'order'  => $order,
                        'filter' => $filterData,
                        'select'=>[
                            '*',
                            'LEAD.TITLE',
                            'DEAL.TITLE',
                            'CONTACT.NAME',
                            'CONTACT.LAST_NAME',
                            'COMPANY.TITLE',
                        ]
                    ]
                );
                $userLink = '/company/personal/user/';
                $permission = Permission::getInstance();
                while ($obj = $objList->fetchObject()) {
                    //Data
                    $active = $obj->getActive() ? 'Y' : 'N';
                    $temp = [
                        'ID'=>$obj->getId(),
                        'NAME'=>$obj->getName(),
                        'AUTHOR'=>'<a href="'.$userLink.$obj->getAuthorId().'/">'.$users[$obj->getAuthorId()].'</a>',
                        'CATEGORY'=>$category[$obj->getCategoryId()],
                        'AMOUNT'=>Money::formatfromBase($obj->getAmount()),
                        'COMMENT_SITUATION'=>$obj->getCommentSituation(),
                        'COMMENT_DATA'=>$obj->getCommentData(),
                        'COMMENT_SOLUTION'=>$obj->getCommentSolution(),
                        'ENTITY'=>Helper::getEntityList()[$obj->getEntityType()].': <a href="'.Helper::getEntityLink($obj->getEntityType()).$obj->getEntityId().'/">'.$obj->getEntityName().'</a>' ,
                        'ACTIVE'=>RequestTemplateTable::getActiveName($active),
                        'VAULT'=>strval($vault[$obj->getVaultId()]),
                    ];
                    
                    //Actions
                    $actions = [];
                    
                    if($permission->isAllowRequestTemplateEdit($obj))
                    {
                        $actions[] = [
                            //Edit
                            'text' => Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.ACTION.EDIT'),
                            'default' => true,
                            'onclick' => $this->makeEditLink($obj->getId()),
                        ];
                    }
                    // Delete
                    if ($permission->isAllowRequestTemplateDelete($obj)) {
                        $actions[] = [
                            'text' => Loc::getMessage('ITB_FIN.REQ_TEMPLATE.LIST.ACTION.DELETE'),
                            'default' => true,
                            'onclick' => 'remove("'.$this->makeRemoveLink($obj->getId()).'");'
                        ];
                    }
                    //Add data
                    $rows[] = [
                        'data' => $temp,
                        'actions' => $actions
                    ];
                    
                }
                //All count for pagination
                $nav->setRecordCount(RequestTemplateTable::getCount($filterData));
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
     * @return string
     */
    protected function makeAddLink()
    {
        $request = Context::getCurrent()->getRequest();
        return "{$request->getRequestedPageDirectory()}/{$this->arResult['URL_TEMPLATES']['add']}";
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
            width: 600
        });";
    }
    
    /**
     * @param $id
     * @return string
     */
    protected function makeRemoveLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?remove=' . $id;
    }
    
    /**
     * @param $userId
     * @param $requestId
     * @return string
     */
    protected function decline($userId, $requestId)
    {
        return 'decline('.$userId.', '.$requestId.')';
    }
}
