<?php

use Bitrix\Main\Context;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Bitrix\Main\UserTable;
use Bitrix\Main\Engine\CurrentUser;

use Itbizon\Finance\Model\RequestTable;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Helper;
use Itbizon\Finance\Permission;
use Itbizon\Finance\Request;
use Itbizon\Finance\Utils\Money;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceRequestList
 */
class CITBFinanceRequestList extends CBitrixComponent
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.ERROR.INCLUDE_FINANCE'));
            }
            if(!$this->arParams['SEF_MODE'] == 'Y') {
                throw new Exception(Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.ERROR.SEF_MODE'));
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
                
                $gridId = 'request_template_list';
                $gridOptions = new Grid\Options($gridId);
                $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
                $order = $sort['sort'];
                $navParams = $gridOptions->GetNavParams();
                
                //Pagination object for grid
                $nav = new UI\PageNavigation($gridId);
                $nav->allowAllRecords(true)->setPageSize($navParams['nPageSize'])->initFromUri();
                
                // Columns for grid
                $columns = [
                    ['id'=>'ID','name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.ID'),'sort' => 'ID','default' => true],
                    ['id'=>'STATUS','name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.STATUS'),'sort' => 'STATUS','default' => true],
                    ['id'=>'NAME','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.NAME"),'sort'=>'NAME','default'=>true,],
                    ['id'=>'DATE_CREATE','name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.DATE_CREATE'),'sort' => 'DATE_CREATE','default' => true],
                    ['id'=>'DATE_APPROVE','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.DATE_APPROVE"),'sort'=>'DATE_APPROVE','default'=>true,],
                    ['id'=>'AUTHOR','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.AUTHOR"),'sort'=>'AUTHOR_ID','default'=>true,],
                    ['id'=>'CATEGORY','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.CATEGORY"),'sort'=>'CATEGORY_ID','default'=>true,],
                    ['id'=>'AMOUNT','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.AMOUNT"),'sort'=>'AMOUNT','default'=>true,],
                    ['id'=>'COMMENT_SITUATION','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.COMMENT_SITUATION"),'sort'=>'COMMENT_SITUATION','default'=>true,],
                    ['id'=>'COMMENT_DATA','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.COMMENT_DATA"),'sort'=>'COMMENT_DATA','default'=>true,],
                    ['id'=>'APPROVER','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.APPROVER"),'sort'=>'APPROVER_ID','default'=>true,],
                    ['id'=>'APPROVER_COMMENT','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.APPROVER_COMMENT"),'sort'=>'APPROVER_COMMENT','default'=>true,],
                    ['id'=>'ENTITY','name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.ENTITY"),'sort'=>'ENTITY_ID','default'=>true,],
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
                
                //Fields for filter
                $filter = [
                    [
                        'id' => 'STATUS',
                        'name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.STATUS'),
                        'type' => 'list',
                        'items'=>RequestTable::getStatuses(),
                        'default' => true,
                        'params'=>[
                            'multiple'=>'Y',
                        ],
                    ],
                    [
                        'id' => 'NAME',
                        'name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.NAME'),
                        'type' => 'string',
                        'default' => true
                    ],
                    [
                        'id' => 'DATE_CREATE',
                        'name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.DATE_CREATE'),
                        'type' => 'date',
                        'default' => true
                    ],
                    [
                        'id' => 'CATEGORY_ID',
                        'name' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.FIELDS.CATEGORY_ID'),
                        'type' => 'list',
                        'items'=>$category,
                        'default' => true,
                        'params'=>[
                            'multiple'=>'Y',
                        ],
                    ],
                ];
                
                // Converting UI filter to D7 filter
                $filterOption = new UI\Filter\Options($gridId);
                $filterData = Helper::FilterUI2D7(
                    $filterOption->getFilter([]),
                    [
                        'search' => ['NAME'],
                        'simple' => [
                            'STATUS',
                            'CATEGORY_ID',
                        ],
                        'date'   => ['DATE_CREATE'],
                    ]
                );
                $filters = $filterOption->getOptions()['filters'];
                if(!isset($filters['default']))
                {
                    $filterOption->setFilterSettings('default', [
                        'name'=>Loc::getMessage("ITB_FIN.REQUEST_TEMPLATE.LIST.START_FILTER.TITLE"),
                        'fields'=> [
                            'STATUS'=>[RequestTable::STATUS_NEW],
                            'DATE_CREATE_datesel'=>'CURRENT_MONTH',
                        ],
                        'filter_rows'=>'STATUS,NAME,DATE_CREATE,CATEGORY_ID',
                    ], true, false);
                    $filterOption->setDefaultPreset('default');
                    $filterOption->save();
                }
                
                if(!CurrentUser::get()->isAdmin() && !Permission::getInstance()->isAllowRequestView())
                    $filterData['=AUTHOR_ID'] = CurrentUser::get()->getId();
                
                // Data for grid
                $rows = [];
                $objList = RequestTable::getList(
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
                $totalAmount = 0;
                while ($obj = $objList->fetchObject()) {
                    //Data
                    $temp = [
                        'ID'=>$obj->getId(),
                        'STATUS'=>$this->getStatusHtml($obj),
                        'NAME'=>$obj->getName(),
                        'DATE_CREATE'=>$obj->getDateCreate(),
                        'DATE_APPROVE'=>$obj->getDateApprove(),
                        'AUTHOR'=>'<a href="'.$userLink.$obj->getAuthorId().'/">'.$users[$obj->getAuthorId()].'</a>',
                        'CATEGORY'=>$category[$obj->getCategoryId()],
                        'AMOUNT'=>Money::formatfromBase($obj->getAmount()),
                        'COMMENT_SITUATION'=>$obj->getCommentSituation(),
                        'COMMENT_DATA'=>$obj->getCommentData(),
                        'APPROVER'=>'<a href="'.$userLink.$obj->getApproverId().'/">'.$users[$obj->getApproverId()].'</a>',
                        'APPROVER_COMMENT'=>$obj->getApproverComment(),
                        'ENTITY'=>Helper::getEntityList()[$obj->getEntityType()].': <a href="'.Helper::getEntityLink($obj->getEntityType()).$obj->getEntityId().'/">'.$obj->getEntityName().'</a>' ,
                    ];
                    $totalAmount += $obj->getAmount();
                    
                    //Actions
                    $actions = [
                        //Edit
                        [
                            'text' => Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.ACTION.EDIT'),
                            'default' => true,
                            'onclick' => $this->makeEditLink($obj->getId()),
                        ],
                    ];
                    if($obj->isAllowCancel())
                    {
                        //Decline
                        $actions[] = [
                            'text'=>Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.ACTION.DECLINE'),
                            'onclick'=>$this->decline(CurrentUser::get()->getId(), $obj->getId()),
                        ];
                    }
                    if($obj->getStatus() == RequestTable::STATUS_CANCEL)
                    {
                        //Retry
                        $actions[] = [
                            'text'=>Loc::getMessage('ITB_FIN.REQUEST_TEMPLATE.LIST.ACTION.RETRY'),
                            'onclick'=>$this->makeRetryLink($obj->getId()),
                        ];
                    }
                    
                    //Add data
                    $rows[] = [
                        'data' => $temp,
                        'actions' => $actions
                    ];
                }
                
                //Total row
                $totalRow[] = [
                    'data' => [
                        'STATUS' => '<b>Итого</b>',
                        'AMOUNT' => '<b>'.Money::formatFromBase($totalAmount).'</b>'
                    ],
                    'actions' => [],
                ];
                $rows = array_merge($totalRow, $rows);

                $currentFilter = [];
                foreach ($filterOption->getFilter([]) as $k => $v) {
                    $currentFilter[$k] = $v;
                }

                //All count for pagination
                $nav->setRecordCount(RequestTable::getCount($filterData));
                $this->arResult = [
                    'GRID_ID' => $gridId,
                    'PAGE' => $componentPage,
                    'NAV' => $nav,
                    'COLUMNS' => $columns,
                    'ROWS' => $rows,
                    'FILTER' => $filter,
                    'CURRENT_FILTER' => $currentFilter,
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
     * @param Request $request
     * @return string
     */
    public function getStatusHtml(Request $request): string
    {
        static $classes = [
            RequestTable::STATUS_NEW     => 'primary',
            RequestTable::STATUS_APPROVE => 'info',
            RequestTable::STATUS_DECLINE => 'danger',
            RequestTable::STATUS_CONFIRM => 'success',
            RequestTable::STATUS_CANCEL  => 'secondary',
            RequestTable::STATUS_FIX     => 'light',
            RequestTable::STATUS_ERROR   => 'dark',
        ];
        $class = isset($classes[$request->getStatus()]) ? $classes[$request->getStatus()] : 'secondary';
        return '<h6><span class="badge badge-'.$class.'">'.$request->getStatusName().'</span></h6>';
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
    
    protected function makeRetryLink($id)
    {
        $path = $this->arResult['FOLDER'] . $this->arResult['URL_TEMPLATES']['add'].'?TEMPLATE_ID='.$id;
        return "BX.SidePanel.Instance.open('{$path}', {
            cacheable: false,
            width: 600
        });";
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

    /**
     * @return string
     */
    public function getAjaxPath(): string
    {
        return $this->GetPath() . '/templates/.default/ajax.php';
    }
}
