<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use ItBizon\Finance\Model\PeriodTable;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\GridHelper;

Loc::loadMessages(__FILE__);

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception(Loc::getMessage('Error load module itbizon.service'));
}

/**
 * Class CITBFinancePeriodList
 */
class CITBFinancePeriodList extends Complex
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if(!Loader::includeModule('itbizon.finance')) {
                throw new Exception(Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.ERROR.INCLUDE_FINANCE'));
            }

            CJSCore::RegisterExt(
                'landInit',
                [
                    "lang" => $this->GetPath() . '/templates/.default/script.js.php',
                ]
            );
            CJSCore::Init(["landInit"]);

            //Route
            $route = $this->initRoute([
                'list'   => 'list/',
                'add'    => 'add/',
                'edit'   => 'edit/#ID#/',
                'income' => 'edit/#ID#/income/',
                'config' => 'config/',
            ], 'list');
            $route->run();

            if($route->getAction() === 'list') {
                //Grid
                $grid = new GridHelper('itb_finance_period_list', 'itb_finance_period_list');
                $grid->setFilter([
                    [
                        'id' => 'ID',
                        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.ID'),
                        'type' => 'number',
                        'default' => true
                    ],
                    [
                        'id' => 'DATE_START',
                        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.DATE_START'),
                        'type' => 'date',
                        'default' => true
                    ],
                    [
                        'id' => 'DATE_END',
                        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.DATE_END'),
                        'type' => 'date',
                        'default' => true
                    ],
                    [
                        'id' => 'STATUS',
                        'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.STATUS'),
                        'type' => 'list',
                        'items' => PeriodTable::getStatus(),
                        'params' => [
                            'multiple' => 'Y'
                        ],
                        'default' => true
                    ],
                ])
                    ->setColumns([
                    ['id' => 'ID', 'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.ID'), 'sort' => 'ID', 'default' => true],
                    ['id' => 'DATE_START', 'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.DATE_START'), 'sort' => 'DATE_START', 'default' => true],
                    ['id' => 'DATE_END', 'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.DATE_END'), 'sort' => 'DATE_END', 'default' => true],
                    ['id' => 'STATUS', 'name' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.FIELDS.STATUS'), 'sort' => 'STATUS', 'default' => true],
                ]);

                $periods = PeriodTable::getList(
                    [
                        'limit' => $grid->getNavigation()->getLimit(),
                        'offset' => $grid->getNavigation()->getOffset(),
                        'order' => $grid->getSort(),
                        'filter' => $grid->getFilterData(),
                    ]
                );
                while ($period = $periods->fetchObject()) {
                    $row = [
                        'data' => [
                            'ID' => $period->getId(),
                            'DATE_START' => $period->getDateStart(),
                            'DATE_END' => $period->getDateEnd(),
                            'STATUS' => $period->getStatusName(),
                        ],
                        'actions' => [
                            [
                                'text' => Loc::getMessage('ITB_FIN.PERIOD_TEMPLATE.LIST.ACTION.EDIT'),
                                'default' => true,
                                'onclick' => 'document.location.href="' . $this->getRoute()->getUrl('edit', ['ID' => $period->getId()]) . '";',
                            ]
                        ]
                    ];
                    $grid->addRow($row);
                }
                $grid->getNavigation()->setRecordCount(PeriodTable::getCount($grid->getFilterData()));
                $this->setGrid($grid);
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage());
        }
        $this->IncludeComponentTemplate();
    }
}
