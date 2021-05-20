<?php

use Bitrix\Main\Grid;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI;
use Itbizon\Scratch;

use Itbizon\Scratch\Model\Box; // fixme

Loc::loadMessages(__FILE__);

/**
 * Class CITBScratchBoxList
 */
class CITBScratchBoxList extends CBitrixComponent
{
    protected $error;

    public function executeComponent()
    {
        try {
        	//*
            CJSCore::RegisterExt(
                'landInit',
                [
                    "lang" => $this->GetPath() . '/templates/.default/script.js.php',
                ]
            );
            CJSCore::Init(["landInit"]);
        	// */

            if (!Loader::includeModule('itbizon.scratch'))
                throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_LIST.ERROR.INCLUDE_FIN'));

            $gridId = 'scratch_box_list';
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
                    'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.ID'),
                    'type' => 'number',
                    'default' => true
                ],
                [
                    'id' => 'NAME',
                    'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.TITLE'),
                    'type' => 'text',
                    'default' => true
                ],
	            [
		            'id' => 'AMOUNT',
		            'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.AMOUNT'),
		            'type' => 'number',
		            'default' => true
	            ],
	            [
		            'id' => 'COUNT',
		            'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.COUNT'),
		            'type' => 'number',
		            'default' => true
	            ],
	            [
		            'id' => 'COMMENT',
		            'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.COMMENT'),
		            'type' => 'text',
		            'default' => true
	            ],

            ];

            //Columns for grid
            $columns = [
                ['id' => 'ID', 'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                ['id' => 'TITLE', 'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.TITLE'), 'sort' => 'TITLE', 'default' => true],
                ['id' => 'AMOUNT', 'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.AMOUNT'), 'sort' => 'AMOUNT', 'default' => true],
                ['id' => 'COUNT', 'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.COUNT'), 'sort' => 'COUNT', 'default' => true],
                ['id' => 'COMMENT', 'name' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.FIELDS.COMMENT'), 'sort' => 'COMMENT', 'default' => true],
            ];

            //Converting UI filter to D7 filter
            $filterOption = new UI\Filter\Options($gridId);
            $filterData = Scratch\Helper::FilterUI2D7(
                $filterOption->getFilter([]),
                [
                    'search' => ['TITLE'],
                    'simple' => ['AMOUNT', 'COUNT', 'COMMENT'],
                    'number' => ['ID' => 1]
                ]
            );

            //Data for grid
            $rows = [];

            $result = Scratch\Model\BoxTable::getList([
                'filter' => $filterData,
                'order' => $sort['sort'],
                'limit' => $nav->getLimit(),
                'offset' => $nav->getOffset()
            ]);

            while ($box = $result->fetchObject()) {
                //Data
                $temp = [
                    'ID' => $box->getId(),
                    'TITLE' => $box->getTitle(),
                    'AMOUNT' => $box->getAmount(),
	                'COUNT' => $box->getCount(),
                    'COMMENT' => $box->getComment(),
                ];

                //Actions
                $actions = [];
                //*
                //Edit
	            $actions[] = [
		            'text' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.ACTION.EDIT'),
		            'default' => true,
		            'onclick' => 'document.location.href="' . $this->makeEditLink($box->getId()) . '";',
	            ];

	            //Delete
	            $actions[] = [
		            'text' => Loc::getMessage('ITB_SCRATCH.BOX_LIST.ACTION.DELETE'),
		            'default' => true,
		            'onclick' => 'remove("' . $this->makeRemoveLink($box->getId()) . '");'
	            ];
                // */

                //Add data
                $rows[] = [
                    'data' => $temp,
                    'actions' => $actions
                ];
            }

            //All count for pagination
            $nav->setRecordCount(Scratch\Model\BoxTable::getCount($filterData));

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

    public function getError()
    {
        return $this->error;
    }

    protected function makeEditLink($id)
    {
        return $this->arParams['FOLDER'] . str_replace('#ID#', $id, $this->arParams['TEMPLATE_EDIT']);
    }

    //*
    protected function makeRemoveLink($id)
    {
        return $this->GetPath() . '/templates/.default/ajax.php?remove=' . $id;
    }
    // */
}
