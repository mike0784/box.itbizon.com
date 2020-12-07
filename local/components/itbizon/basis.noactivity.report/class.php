<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Basis\Utils\HelperActivityReport;

/**
 * Class CITBBasisNoActivityReport
 */
class CITBBasisNoActivityReport extends CBitrixComponent
{
    /**
     * @return mixed|void|null
     * @throws Exception
     */
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.basis')) {
                throw new Exception('Ошибка подключения модуля itbizon.basis');
            }
            if (!Loader::includeModule('crm')) {
                throw new Exception('Ошибка подключения модуля crm');
            }

            $gridId = 's_no_activity_report';
            $gridOptions = new Bitrix\Main\Grid\Options($gridId);
            $sort = $gridOptions->GetSorting([
                'sort' => ['ID' => 'DESC'], 'vars' => ['by' => 'by', 'order' => 'order']
            ]);
            $order = $sort['sort'];
            $navParams = $gridOptions->GetNavParams();
            // Pagination object for grid
            $nav = new Bitrix\Main\UI\PageNavigation($gridId);
            $nav->allowAllRecords(true)->setPageSize($navParams['nPageSize'])->initFromUri();

            // Columns for grid
            $columns = [
                ['id' => 'ID', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
                ['id' => 'FULL_NAME', 'name' => 'Ответственный', 'sort' => '', 'default' => true],
                ['id' => 'LEADS_DEALS', 'name' => 'Лид/Сделка название', 'sort' => 'TITLE', 'default' => true],
                ['id' => 'DATE_CREATE', 'name' => 'Дата создания', 'sort' => 'DATE_CREATE', 'default' => true],
            ];

            //Fields for filter
            $filter = [
                [
                    'id' => 'PERIOD',
                    'name' => 'Период',
                    'type' => 'list',
                    "items" => array(
                        "DAY" => "День",
                        "WEEK" => "Неделя",
                        "MONTH" => "Месяц",
                    )
                ],
                [
                    'id' => 'VALUE',
                    'name' => 'Значение',
                    'type' => 'string',
                ],
                [
                    'id' => 'TYPE_ENTITY',
                    'name' => 'Тип',
                    'type' => 'list',
                    "items" => array(
                        CCrmOwnerType::Lead => "Лид",
                        CCrmOwnerType::Deal => "Сделка",
                    )
                ],
            ];

            // Converting UI filter to D7 filter
            $filterOption = new \Bitrix\Main\UI\Filter\Options($gridId);
            $filterData = $filterOption->getFilterLogic($filter);
            $filterOption->setupDefaultFilter([
                'VALUE' => intval($filterData['VALUE']) > 0 ? intval($filterData['VALUE']) : 1,
                'PERIOD' => !isset($filterData['PERIOD']) ? "MONTH" : $filterData['PERIOD'],
                'TYPE_ENTITY' => !isset($filterData['TYPE_ENTITY']) ? CCrmOwnerType::Deal : $filterData['TYPE_ENTITY'],
            ], ['VALUE', 'PERIOD', 'TYPE_ENTITY']);

            $filterData = $filterOption->getFilterLogic($filter);

            $modifyDate = '-' . $filterData['VALUE'] . " " . strtolower($filterData['PERIOD']);
            $dateFrom = (new \DateTime())->setTime(00, 00, 00)->modify($modifyDate);
            $dateTo = (new \DateTime())->setTime(23, 59, 59);

            $leadsDealsIds = [];
            $result = \Bitrix\Crm\ActivityTable::getList([
                'select' => ['OWNER_ID', 'OWNER_TYPE_ID', 'CREATED'],
                'filter' => [
                    '>=CREATED' => $dateFrom->format('d.m.Y H:i:s'),
                    '<=CREATED' => $dateTo->format('d.m.Y H:i:s'),
                    '=OWNER_TYPE_ID' => [CCrmOwnerType::Lead, CCrmOwnerType::Deal]
                ]
            ]);
            while ($row = $result->fetch()) {
                $leadsDealsIds[] = $row['OWNER_ID'];
            }

            $timeLineTable = \Bitrix\Crm\Timeline\Entity\TimelineTable::getList([
                'select' => ['TYPE_ID', 'CREATED', 'BINDINGS'],
                'filter' => [
                    '>=CREATED' => $dateFrom->format('d.m.Y H:i:s'),
                    '<=CREATED' => $dateTo->format('d.m.Y H:i:s'),
                    '=TYPE_ID' => 7, //Тип комментария
                    '=CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_TYPE_ID' => [CCrmOwnerType::Lead, CCrmOwnerType::Deal]
                ]
            ]);
            while ($row = $timeLineTable->fetch()) {
                $leadsDealsIds[] = $row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID'];
            }
            $leadsDealsIds = array_unique($leadsDealsIds);

            $rows = [];
            if ($filterData['TYPE_ENTITY'] == CCrmOwnerType::Lead) {
                $resultLeads = \Bitrix\Crm\LeadTable::getList([
                    'select' => [
                        'ID',
                        'TITLE',
                        'DATE_CREATE',
                        'ASSIGNED_ID' => 'ASSIGNED_BY.ID',
                        'ASSIGNED_LAST_NAME' => 'ASSIGNED_BY.LAST_NAME',
                        'ASSIGNED_NAME' => 'ASSIGNED_BY.NAME',
                        'STATUS_SEMANTIC_ID'
                    ],
                    'filter' => [
                        '=STATUS_SEMANTIC_ID' => 'P',
                        '!=ID' => $leadsDealsIds
                    ],
                    'order' => $order,
                ]);

                while ($row = $resultLeads->fetch()) {
                    $rows[] = $this->makeRow($row, CCrmOwnerType::Lead);
                }
            }

            if ($filterData['TYPE_ENTITY'] == CCrmOwnerType::Deal) {
                $resultDeals = \Bitrix\Crm\DealTable::getList([
                    'select' => [
                        'ID',
                        'TITLE',
                        'DATE_CREATE',
                        'ASSIGNED_ID' => 'ASSIGNED_BY.ID',
                        'ASSIGNED_LAST_NAME' => 'ASSIGNED_BY.LAST_NAME',
                        'ASSIGNED_NAME' => 'ASSIGNED_BY.NAME',
                        'STAGE_SEMANTIC_ID'
                    ],
                    'filter' => [
                        '=STAGE_SEMANTIC_ID' => 'P',
                        '!=ID' => $leadsDealsIds
                    ],
                    'order' => $order,
                ]);

                while ($row = $resultDeals->fetch()) {
                    $rows[] = $this->makeRow($row, CCrmOwnerType::Deal);
                }
            }

            $nav->setRecordCount(count($rows));
            $pageNumber = isset($_GET[$gridId]) ? strval($_GET[$gridId]) : 0;
            if (is_string($pageNumber)) {
                if ($pageNumber != 'page-all') {
                    $pageNumber = intval(str_replace('page-', '', $_GET[$gridId])) - 1;
                    $rows = array_chunk($rows, $nav->getLimit())[$pageNumber];
                }
            } else {
                $rows = array_chunk($rows, $nav->getLimit())[$pageNumber];
            }

            $this->arResult = [
                'GRID_ID' => $gridId,
                'COLUMNS' => $columns,
                'ROWS' => $rows,
                'FILTER' => $filter,
                'NAV' => $nav,
            ];
        } catch (Exception $e) {
            $this->arResult = [
                'ERROR_MESSAGE' => $e->getMessage(),
            ];
        }
        // Include template
        $this->IncludeComponentTemplate();
    }

    /**
     * @param array $data
     * @param int $type
     * @return array
     */
    private function makeRow(array $data, int $type): array
    {
        $urlLeadOrDel = '#';
        if ($type === CCrmOwnerType::Lead) {
            $urlLeadOrDel = "/crm/lead/details/{$data['ID']}/";
        }
        if ($type === CCrmOwnerType::Deal) {
            $urlLeadOrDel = "/crm/deal/details/{$data['ID']}/";
        }
        return [
            'data' => [
                'ID' => $data['ID'],
                'FULL_NAME' => "<a href='/company/personal/user/{$data['ASSIGNED_ID']}/'>{$data['ASSIGNED_LAST_NAME']} {$data['ASSIGNED_NAME']}</a>",
                'LEADS_DEALS' => "<a href='$urlLeadOrDel'>{$data['TITLE']}</a>",
                'DATE_CREATE' => $data['DATE_CREATE'],
            ],
        ];
    }
}
