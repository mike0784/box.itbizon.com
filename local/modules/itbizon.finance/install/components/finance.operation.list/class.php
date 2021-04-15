<?php

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Bitrix\Main\UserTable;
use Itbizon\Finance;
use Itbizon\Finance\Model\OperationTable;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceOperationList
 */
class CITBFinanceOperationList extends CBitrixComponent
{
    protected $error;

    /**
     * @return bool|mixed
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
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_LIST.ERROR.INCLUDE_FIN'));

            if (!Loader::IncludeModule('crm'))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_LIST.ERROR.INCLUDE_CRM'));

            $gridId = 'finance_operation_list1';
            $gridOptions = new Grid\Options($gridId);
            $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
            $navParams = $gridOptions->GetNavParams();

            $users = [];
            $usersList = UserTable::getList([
                'select' => ['ID', 'NAME', 'LAST_NAME']
            ]);
            while ($user = $usersList->fetchObject())
                $users[$user->getId()] = $user->getName() . " " . $user->getLastName();

            $vaultsList = Finance\Model\VaultTable::getList([
                'select' => ['ID', 'NAME'],
                'filter' => ['!=TYPE' => Finance\Model\VaultTable::TYPE_STOCK]
            ])->fetchAll();
            $vaults = $categories = array_combine(
                array_column($vaultsList, 'ID'),
                array_column($vaultsList, 'NAME')
            );

            $categoriesList = Finance\Model\OperationCategoryTable::getList([
                'select' => ['ID', 'NAME']
            ])->fetchAll();
            $categories = array_combine(
                array_column($categoriesList, 'ID'),
                array_column($categoriesList, 'NAME')
            );

            $crmList = [
                CCrmOwnerType::Lead => CCrmOwnerType::GetDescription(CCrmOwnerType::Lead),
                CCrmOwnerType::Deal => CCrmOwnerType::GetDescription(CCrmOwnerType::Deal),
                CCrmOwnerType::Company => CCrmOwnerType::GetDescription(CCrmOwnerType::Company),
                CCrmOwnerType::Contact => CCrmOwnerType::GetDescription(CCrmOwnerType::Contact),
            ];

            //Pagination object for grid
            $nav = new UI\PageNavigation($gridId);
            $nav->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            //Fields for filter
            $filter = [
                [
                    'id' => 'ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.ID'),
                    'type' => 'number',
                    'default' => true
                ],
                [
                    'id' => 'EXTERNAL_CODE',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.EXTERNAL_CODE'),
                    'type' => 'text',
                    'default' => true
                ], [
                    'id' => 'NAME',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.NAME'),
                    'type' => 'text',
                    'default' => true
                ],
                [
                    'id' => 'AMOUNT',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.AMOUNT'),
                    'type' => 'number',
                    'default' => true
                ],
                [
                    'id' => 'CATEGORY_ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.CATEGORY_ID'),
                    'type' => 'list',
                    'items' => $categories,
                    'default' => true
                ],
                [
                    'id' => 'TYPE',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.TYPE'),
                    'type' => 'list',
                    'items' => OperationTable::getType(),
                    'default' => true
                ],
                [
                    'id' => 'VAULT_ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.VAULT'),
                    'type' => 'list',
                    'items' => $vaults,
                    'default' => true
                ],
                [
                    'id' => 'ENTITY_TYPE_ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.EXTERNAL_CODE'),
                    'type' => 'list',
                    'items' => $crmList,
                    'default' => true
                ],
                [
                    'id' => 'ENTITY_ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.ENTITY_ID'),
                    'type' => 'text',
                    'default' => true
                ],
                [
                    'id' => 'RESPONSIBLE_ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.RESPONSIBLE'),
                    'type' => 'list',
                    'items' => $users,
                    'params' => [
                        'multiple' => 'Y'
                    ],
                    'default' => true,
                ],
                [
                    'id' => 'STATUS',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.STATUS'),
                    'type' => 'list',
                    'items' =>  [
                        OperationTable::STATUS_ERROR => OperationTable::getStatus(OperationTable::STATUS_ERROR),
                        OperationTable::STATUS_NEW => OperationTable::getStatus(OperationTable::STATUS_NEW),
                        OperationTable::STATUS_PLANNING => OperationTable::getStatus(OperationTable::STATUS_PLANNING),
                        OperationTable::STATUS_DECLINE => OperationTable::getStatus(OperationTable::STATUS_DECLINE),
                        OperationTable::STATUS_COMMIT => OperationTable::getStatus(OperationTable::STATUS_COMMIT),
                        OperationTable::STATUS_CANCEL => OperationTable::getStatus(OperationTable::STATUS_CANCEL),
                    ],
                    'params' => [
                        'multiple' => 'Y'
                    ],
                    'default' => true,
                ],
                [
                    'id' => 'DATE_CREATE',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.DATE_CREATE'),
                    'type' => 'date',
                    'default' => true
                ],
                [
                    'id' => 'DATE_COMMIT',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.DATE_COMMIT'),
                    'type' => 'date',
                    'default' => true
                ],
                [
                    'id' => 'REQUEST_ID',
                    'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.REQUEST_ID'),
                    'type' => 'number',
                    'default' => true
                ],
            ];

            //Columns for grid
            $columns = [
                ['id' => 'ID', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                ['id' => 'NAME', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.NAME'), 'sort' => 'NAME', 'default' => true],
                ['id' => 'TYPE', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.TYPE'), 'sort' => 'TYPE', 'default' => true],
                ['id' => 'STATUS', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.STATUS'), 'sort' => 'STATUS', 'default' => true],
                ['id' => 'CATEGORY_ID', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.CATEGORY_ID'), 'sort' => 'CATEGORY_ID', 'default' => true],
                ['id' => 'CRM', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.CRM'), 'sort' => 'ENTITY_ID', 'default' => true],
                ['id' => 'AMOUNT', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.AMOUNT'), 'sort' => 'AMOUNT', 'default' => true],
                ['id' => 'RESPONSIBLE', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.RESPONSIBLE'), 'sort' => 'RESPONSIBLE_ID', 'default' => true],
                ['id' => 'DATE_CREATE', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.DATE_CREATE'), 'sort' => 'DATE_CREATE', 'default' => true],
                ['id' => 'DATE_COMMIT', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.DATE_COMMIT'), 'sort' => 'DATE_COMMIT', 'default' => true],
                ['id' => 'SRC_VAULT', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.SRC_VAULT'), 'sort' => 'SRC_VAULT_ID', 'default' => true],
                ['id' => 'DST_VAULT', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.DST_VAULT'), 'sort' => 'DST_VAULT_ID', 'default' => true],
                ['id' => 'COMMENT', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.COMMENT'), 'sort' => 'COMMENT', 'default' => true],
                ['id' => 'EXTERNAL_CODE', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.EXTERNAL_CODE'), 'sort' => 'EXTERNAL_CODE', 'default' => true],
                ['id' => 'REQUEST_ID', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.REQUEST_ID'), 'sort' => 'REQUEST_ID', 'default' => true],
                ['id' => 'FILE', 'name' => Loc::getMessage('ITB_FIN.OPERATION_LIST.FIELDS.FILE'), 'sort' => 'FILE_ID', 'default' => true],
            ];

            $lang = Bitrix\Main\Application::getInstance()->getContext()->getLanguage();
            $entityId = Finance\Model\OperationTable::getUfId();
            $manager = new \CUserTypeManager();
            $userFieldsColumns = $manager->GetUserFields($entityId, 0, $lang);
            foreach ($userFieldsColumns as $key => $val) {
                $columns[] = [
                    'id' => $key, 'name' => $val['EDIT_FORM_LABEL'], 'sort' => null, 'default' => false
                ];
            }

            //Converting UI filter to D7 filter
            $filterOption = new UI\Filter\Options($gridId);
            $filterList = $filterOption->getFilter([]);
            $filterData = Finance\Helper::FilterUI2D7(
                $filterList,
                [
                    'search' => [
                        'NAME',
                        'EXTERNAL_CODE'
                    ],
                    'simple' => [
                        'TYPE',
                        'RESPONSIBLE_ID',
                        'STATUS',
                        'CATEGORY_ID',
                        'ENTITY_TYPE_ID',
                        'ENTITY_ID'
                    ],
                    'number' => [
                        'ID' => 1,
                        'AMOUNT' => 100,
                        'REQUEST_ID' => 1
                    ],
                    'date' => [
                        'DATE_CREATE',
                        'DATE_COMMIT'
                    ]
                ]
            );

            if (isset($filterList['VAULT_ID'])) {
                $filterData = [
                    'LOGIC' => 'AND',
                    [$filterData],
                    [
                        'LOGIC' => 'OR',
                        '=DST_VAULT_ID' => intval($filterList['VAULT_ID']),
                        '=SRC_VAULT_ID' => intval($filterList['VAULT_ID']),
                    ]
                ];
            }

            //Data for grid
            $rows = [];

            // Настройка фильтра
            $currentUser = CurrentUser::get();
            if (!Finance\Permission::getInstance()->isAllowOperationView()) {
                $filterData = [
                    'LOGIC' => 'AND',
                    [
                        $filterData
                    ],
                    [
                        [
                            'LOGIC' => 'OR',
                            [
                                '=RESPONSIBLE_ID' => $currentUser->getId()
                            ],
                            [
                                'LOGIC' => 'OR',
                                [
                                    '=SRC_VAULT.RESPONSIBLE_ID' => $currentUser->getId()
                                ],
                                [
                                    '=DST_VAULT.RESPONSIBLE_ID' => $currentUser->getId(),
                                ]
                            ],
                        ]
                    ]
                ];
            }
            //$filterData['!=SRC_VAULT.TYPE'] = Finance\Model\VaultTable::TYPE_STOCK;
            //$filterData['!=DST_VAULT.TYPE'] = Finance\Model\VaultTable::TYPE_STOCK;

            $total = [
                'NAME' => '<b>'.Loc::getMessage('ITB_FIN.OPERATION_LIST.TOTAL').'</b>',
                'AMOUNT' => 0
            ];

            $result = OperationTable::getList([
                'filter' => $filterData,
                'order' => $sort['sort'],
                'limit' => $nav->getLimit(),
                'offset' => $nav->getOffset(),
            ]);

            while ($operation = $result->fetchObject()) {
                $srcVal = $operation->getSrcVault();
                $srcLink = "-";
                if (isset($srcVal))
                    $srcLink = '<a target="_blank" href="' . $srcVal->getUrl() . '">' . $srcVal->getName() . '</a>';

                $dstVal = $operation->getDstVault();
                $dstLink = "-";
                if (isset($dstVal))
                    $dstLink = '<a target="_blank" href="' . $dstVal->getUrl() . '">' . $dstVal->getName() . '</a>';

                $requestLink = '-';
                if ($operation->getRequestId()) {
                    $requestLink = '<a target="_blank" href="/finance/request/edit/'.$operation->getRequestId().'/">'.$operation->getRequestId().'</a>';
                }

                $fileLink = '';
                if($operation->getFileId() > 0) {
                    $fileLink = '<a target="_blank" href="'.$operation->getFileUrl().'" download>Скачать</a>';
                }

                $statusClass = '';
                switch ($operation->getStatus()) {
                    case OperationTable::STATUS_NEW:
                        $statusClass = 'text-warning';
                        break;
                    case OperationTable::STATUS_DECLINE:
                        $statusClass = 'text-danger';
                        break;
                    case OperationTable::STATUS_COMMIT:
                        $statusClass = 'text-success';
                        break;
                    case OperationTable::STATUS_CANCEL:
                        $statusClass = 'text-secondary';
                        break;
                    case OperationTable::STATUS_PLANNING:
                        // no break
                    case OperationTable::STATUS_ERROR:
                        $statusClass = '';
                        break;
                }

                //Data
                $temp = [
                    'ID' => $operation->getId(),
                    'NAME' => $operation->getName(),
                    'TYPE' => $operation->getTypeName(),
                    'CATEGORY_ID' => ($operation->getCategory()) ? $operation->getCategory()->getName() : '',
                    'STATUS' => '<span class="' . $statusClass . '">' . $operation->getStatusName() . '</span>',
                    'AMOUNT' => Finance\Utils\Money::formatFromBase($operation->getAmount()),
                    'DATE_CREATE' => $operation->getDateCreate(),
                    'DATE_COMMIT' => $operation->getDateCommit(),
                    'COMMENT' => $operation->getComment(),
                    'EXTERNAL_CODE' => $operation->getExternalCode(),
                    'REQUEST_ID' => $requestLink,
                    'CRM' => '<a href="' . $operation->getEntityUrl() . '">' . $operation->getEntityName() . '</a>',
                    'SRC_VAULT' => $srcLink,
                    'DST_VAULT' => $dstLink,
                    'RESPONSIBLE' => '<a href="' . $operation->getResponsibleUrl() . '">' . $operation->getResponsibleName() . '</a>',
                    'FILE' => $fileLink,
                ];

                $userFieldsValues = $manager->GetUserFields($entityId, $operation->getId(), $lang);
                foreach ($userFieldsValues as $ufkey => $ufval) {
                    $temp[$ufkey] = $ufval['VALUE'];
                }

                //Actions
                $actions = [];

                if(
                    ($operation->getSrcVault() && $operation->getSrcVault()->getType() !== Finance\Model\VaultTable::TYPE_STOCK) ||
                    ($operation->getDstVault() && $operation->getDstVault()->getType() !== Finance\Model\VaultTable::TYPE_STOCK)
                ) {
                    //Edit
                    if (Finance\Permission::getInstance()->isAllowOperationView($operation)) {
                        $actions[] = [
                            'text' => Loc::getMessage('ITB_FIN.OPERATION_LIST.ACTION.EDIT'),
                            'default' => true,
                            'onclick' => 'document.location.href="' . $this->makeEditLink($operation->getId()) . '";',
                        ];
                    }
                    //Delete
                    if (Finance\Permission::getInstance()->isAllowOperationDelete($operation)) {
                        $actions[] = [
                            'text' => Loc::getMessage('ITB_FIN.OPERATION_LIST.ACTION.DELETE'),
                            'default' => true,
                            'onclick' => 'request("' .
                                $this->makeRemoveLink($operation->getId()) . '", "' . Loc::getMessage('ITB_FIN.OPERATION_LIST.MESS.CONFIRM_DELETE') . '");'
                        ];
                    }

                    // Accept
                    if ($operation->isAllowConfirmBy($currentUser->getId())) {
                        $actions[] = [
                            'text' => Loc::getMessage('ITB_FIN.OPERATION_LIST.ACTION.ACCEPT'),
                            'default' => true,
                            'onclick' => 'request("' . $this->makeAcceptLink($operation->getId()) . '");'
                        ];
                    }

                    // Decline
                    if ($operation->isAllowDeclineBy($currentUser->getId())) {
                        $actions[] = [
                            'text' => Loc::getMessage('ITB_FIN.OPERATION_LIST.ACTION.DECLINE'),
                            'default' => true,
                            'onclick' => 'request("' . $this->makeDeclineLink($operation->getId()) . '");'
                        ];
                    }

                    // Cancel
                    if ($operation->isAllowRollbackBy($currentUser->getId())) {
                        $actions[] = [
                            'text' => Loc::getMessage('ITB_FIN.OPERATION_LIST.ACTION.CANCEL'),
                            'default' => true,
                            'onclick' => 'request("' . $this->makeCancelLink($operation->getId()) . '", "' . Loc::getMessage('ITB_FIN.OPERATION_LIST.MESS.CONFIRM_CANCEL') . '");'
                        ];
                    }
                }

                //Add data
                $rows[] = [
                    'data' => $temp,
                    'actions' => $actions
                ];

                //Calc total row
                $amount = 0;
                if($operation->getType() === OperationTable::TYPE_INCOME)
                    $amount = $operation->getAmount();
                else if($operation->getType() === OperationTable::TYPE_OUTGO)
                    $amount = -$operation->getAmount();
                $total['AMOUNT'] += $amount;
            }

            //Total row
            array_unshift($rows, [
                'data' => [
                    'NAME' => $total['NAME'],
                    'AMOUNT' => '<b>'.Finance\Utils\Money::formatFromBase($total['AMOUNT']).'</b>'
                ],
            ]);

            //All count for pagination
            $nav->setRecordCount(OperationTable::getCount($filterData));

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
    public function makeEditLink($id)
    {
        return $this->arParams['FOLDER'] . str_replace('#ID#', $id, $this->arParams['TEMPLATE_EDIT']);
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeRemoveLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?id=' . $id . '&action=remove';
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeAcceptLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?id=' . $id . '&action=accept';
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeDeclineLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?id=' . $id . '&action=decline';
    }

    /**
     * @param $id
     * @return string
     */
    protected function makeCancelLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?id=' . $id . '&action=cancel';
    }
}
