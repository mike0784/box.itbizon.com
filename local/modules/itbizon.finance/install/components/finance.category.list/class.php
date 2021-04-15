<?php

use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Itbizon\Finance;

Loc::loadMessages(__FILE__);

/**
 * Class CITBFinanceCategoryList
 */
class CITBFinanceCategoryList extends CBitrixComponent
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
                throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_LIST.ERROR.INCLUDE_FIN'));

            $gridId = 'finance_category_list';
            $gridOptions = new Grid\Options($gridId);
            $sort = $gridOptions->GetSorting(['sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']]);
            $navParams = $gridOptions->GetNavParams();

            //Pagination object for grid
            $nav = new UI\PageNavigation($gridId);
            $nav->allowAllRecords(true)
                ->setPageSize($navParams['nPageSize'])
                ->initFromUri();

            //Fields for filter
            $filter = [
                [
                    'id' => 'ID',
                    'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ID'),
                    'type' => 'number',
                    'default' => true
                ],
                [
                    'id' => 'NAME',
                    'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.NAME'),
                    'type' => 'text',
                    'default' => true
                ],
                [
                    'id' => 'ALLOW_INCOME',
                    'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ALLOW_INCOME'),
                    'type' => 'checkbox',
                    'default' => true
                ],
                [
                    'id' => 'ALLOW_OUTGO',
                    'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ALLOW_OUTGO'),
                    'type' => 'checkbox',
                    'default' => true
                ],
                [
                    'id' => 'ALLOW_TRANSFER',
                    'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ALLOW_TRANSFER'),
                    'type' => 'checkbox',
                    'default' => true
                ],
            ];

            //Columns for grid
            $columns = [
                ['id' => 'ID', 'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                ['id' => 'NAME', 'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.NAME'), 'sort' => 'NAME', 'default' => true],
                ['id' => 'ALLOW_INCOME', 'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ALLOW_INCOME'), 'sort' => 'ALLOW_INCOME', 'default' => true],
                ['id' => 'ALLOW_OUTGO', 'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ALLOW_OUTGO'), 'sort' => 'ALLOW_OUTGO', 'default' => true],
                ['id' => 'ALLOW_TRANSFER', 'name' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.FIELDS.ALLOW_TRANSFER'), 'sort' => 'ALLOW_TRANSFER', 'default' => true],
            ];

            //Converting UI filter to D7 filter
            $filterOption = new UI\Filter\Options($gridId);
            $filterData = Finance\Helper::FilterUI2D7(
                $filterOption->getFilter([]),
                [
                    'search' => ['NAME'],
                    'simple' => ['ALLOW_INCOME', 'ALLOW_OUTGO', 'ALLOW_TRANSFER'],
                    'number' => ['ID' => 1]
                ]
            );

            //Data for grid
            $rows = [];

            $result = Finance\Model\OperationCategoryTable::getList([
                'filter' => $filterData,
                'order' => $sort['sort'],
                'limit' => $nav->getLimit(),
                'offset' => $nav->getOffset()
            ]);

            while ($category = $result->fetchObject()) {
                //Data
                $temp = [
                    'ID' => $category->getId(),
                    'NAME' => $category->getName(),
                    'ALLOW_INCOME' => $category->getAllowIncome() ? Loc::getMessage('ITB_FIN.CATEGORY_LIST.YES') : Loc::getMessage('ITB_FIN.CATEGORY_LIST.NO'),
                    'ALLOW_OUTGO' => $category->getAllowOutgo() ? Loc::getMessage('ITB_FIN.CATEGORY_LIST.YES') : Loc::getMessage('ITB_FIN.CATEGORY_LIST.NO'),
                    'ALLOW_TRANSFER' => $category->getAllowTransfer() ? Loc::getMessage('ITB_FIN.CATEGORY_LIST.YES') : Loc::getMessage('ITB_FIN.CATEGORY_LIST.NO'),
                ];

                //Actions
                $actions = [];
                //Edit
                if (Finance\Permission::getInstance()->isAllowCategoryView($category)) {
                    $actions[] = [
                        'text' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.ACTION.EDIT'),
                        'default' => true,
                        'onclick' => 'document.location.href="' . $this->makeEditLink($category->getId()) . '";',
                    ];
                }
                //Delete
                if (Finance\Permission::getInstance()->isAllowCategoryDelete($category)) {
                    $actions[] = [
                        'text' => Loc::getMessage('ITB_FIN.CATEGORY_LIST.ACTION.DELETE'),
                        'default' => true,
                        'onclick' => 'remove("' . $this->makeRemoveLink($category->getId()) . '");'
                    ];
                }

                //Add data
                $rows[] = [
                    'data' => $temp,
                    'actions' => $actions
                ];

            }

            //All count for pagination
            $nav->setRecordCount(Finance\Model\OperationCategoryTable::getCount($filterData));

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
    public function makeAddLink()
    {
        return $this->arParams['FOLDER'] . $this->arParams['TEMPLATE_ADD'];
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param $id
     * @return string
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
        return $this->GetPath() . '/templates/.default/ajax.php?remove=' . $id;
    }
}
