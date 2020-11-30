<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Basis\Utils\HelperActivityReport;

/**
 * Class CITBBasisActivityReport2
 */
class CITBBasisActivityReport2 extends CBitrixComponent
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
            $customFieldLead = trim(COption::GetOptionString("itbizon.basis", "date_last_activity_lead"));
            $customFieldDeal = trim(COption::GetOptionString("itbizon.basis", "date_last_activity_deal"));

            if (!$customFieldLead && !$customFieldDeal) {
                throw new \Exception('Поле даты последней активности лида и сделки не заполнены');
            }
            CJSCore::RegisterExt(
                'landInit',
                [
                    "lang" => $this->GetPath() . '/templates/.default/script.js.php',
                ]
            );
            CJSCore::Init(["landInit"]);

            $gridId = 's_activity_report2';
            $gridOptions = new Bitrix\Main\Grid\Options($gridId);
            $sort = $gridOptions->GetSorting([
                'sort' => ['LAST_NAME' => 'ASC'], 'vars' => ['by' => 'by', 'order' => 'order']
            ]);
            $order = $sort['sort'];
            $navParams = $gridOptions->GetNavParams();
            // Pagination object for grid
            $nav = new Bitrix\Main\UI\PageNavigation($gridId);
            $nav->allowAllRecords(true)->setPageSize($navParams['nPageSize'])->initFromUri();

            // Columns for grid
            $columns = [
                ['id' => 'FULL_NAME', 'name' => 'Сотрудник', 'sort' => 'LAST_NAME', 'default' => true],
                ['id' => 'LEADS', 'name' => 'Лиды', 'sort' => '', 'default' => true],
                ['id' => 'DEALS', 'name' => 'Сделки', 'sort' => '', 'default' => true],
                ['id' => 'TOTAL', 'name' => 'Итого', 'sort' => '', 'default' => true],
            ];

            //Fields for filter
            $filter = [
                [
                    'id' => 'USER_ID',
                    'name' => 'Сотрудник',
                    'type' => 'dest_selector',
                    'params' => [
                        'multiple' => 'Y',
                    ],
                    'default' => true
                ],
                [
                    'id' => 'PERIOD',
                    'name' => 'Период',
                    'type' => 'date',
                    'default' => false
                ],
            ];

            // Converting UI filter to D7 filter
            $filterOption = new \Bitrix\Main\UI\Filter\Options($gridId);
            $filterData = $filterOption->getFilterLogic($filter);
            if (!isset($filterData['>=PERIOD']) && !isset($filterData['<=PERIOD'])) {
                $filterOption->setupDefaultFilter([
                    'PERIOD_datesel' => 'CURRENT_MONTH',
                ], ['PERIOD']);
                $filterData = $filterOption->getFilterLogic($filter);
            }

            $users = [];
            $result = UserTable::getList([
                'select' => ['ID', 'LAST_NAME', 'NAME'],
                'filter' => HelperActivityReport::prepareFilterUsers($filterData),
                'order' => $order,
            ]);
            while ($row = $result->fetch()) {
                $users[$row['ID']] = $row;
            }
            if (!$users) {
                throw new \Exception('Активных сотрудников не найдено');
            }
            unset($filterData['USER_ID']);
            $userIds = array_unique(array_keys($users));

            foreach ($filterData as $key => $val) {
                $filterData[str_replace('PERIOD', 'CREATED', $key)] = $val;
                unset($filterData[$key]);
            }

            $activities = \Bitrix\Crm\ActivityTable::getList([
                'select' => ['OWNER_ID', 'OWNER_TYPE_ID', 'CREATED', 'AUTHOR_ID'],
                'filter' => array_merge([
                    '=AUTHOR_ID' => $userIds,
                    '=OWNER_TYPE_ID' => [CCrmOwnerType::Lead, CCrmOwnerType::Deal]
                ], $filterData)
            ]);

            while ($row = $activities->fetch()) {
                if ($row['OWNER_TYPE_ID'] == CCrmOwnerType::Lead) {
                    $users[$row['AUTHOR_ID']]['LEADS'][$row['OWNER_ID']][] = $row;
//                        $leads[$row['OWNER_ID']][] = $row;
                }
                if ($row['OWNER_TYPE_ID'] == CCrmOwnerType::Deal) {
//                        $deals[$row['OWNER_ID']][] = $row;
                    $users[$row['AUTHOR_ID']]['DEALS'][$row['OWNER_ID']][] = $row;
                }
            }

            $timeLineTable = \Bitrix\Crm\Timeline\Entity\TimelineTable::getList([
                'select' => ['*', 'BINDINGS'],
                'filter' => array_merge([
                    '=TYPE_ID' => 7, //Тип комментария
                    '=AUTHOR_ID' => $userIds,
                    '=CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_TYPE_ID' => [CCrmOwnerType::Lead, CCrmOwnerType::Deal]
                ], $filterData)
            ]);

            while ($row = $timeLineTable->fetch()) {
                if ($row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_TYPE_ID'] == CCrmOwnerType::Lead) {
                    $users[$row['AUTHOR_ID']]['LEADS'][$row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID']][] = $row;
//                        $leads[$row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID']][] = $row;
                }
                if ($row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_TYPE_ID'] == CCrmOwnerType::Deal) {
                    $users[$row['AUTHOR_ID']]['DEALS'][$row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID']][] = $row;
//                        $deals[$row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID']][] = $row;
                }
            }

            $rows = [];

            foreach ($users as $user) {
                $userId = $user['ID'];
                $countLeads = count(array_unique(array_keys($user['LEADS'])));
                $countDeals = count(array_unique(array_keys($user['DEALS'])));
                $data = [
                    'data' => [
                        'ID' => $userId['ID'],
                        'FULL_NAME' => "<a href='/company/personal/user/{$userId}/'>{$user['LAST_NAME']} {$user['NAME']}</a>",
                        'LEADS' => "<a href='#' class='show__popup' data-path='{$this->makeLinkShowLeads($userId,$filterData)}'>{$countLeads}</a>",
                        'DEALS' => "<a href='#' class='show__popup' data-path='{$this->makeLinkShowDeals($userId,$filterData)}'>{$countDeals}</a>",
                        'TOTAL' => $countLeads + $countDeals,
                    ],
                ];
                if ($countLeads || $countDeals) {
                    $rows[] = $data;
                }
            }

            $rows = isset($rows) ? $rows : [];
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
     * @param int $userId
     * @param array $filter
     * @return string
     */
    protected function makeLinkShowLeads(int $userId, array $filter): string
    {
        return $this->GetPath() . '/templates/.default/ajax.php?cmd=getLeadsPopup&userId=' . $userId . '&' . http_build_query(['filter' => $filter]);
    }

    /**
     * @param int $userId
     * @param array $filter
     * @return string
     */
    protected function makeLinkShowDeals(int $userId, array $filter): string
    {
        return $this->GetPath() . '/templates/.default/ajax.php?cmd=getDealsPopup&userId=' . $userId . '&' . http_build_query(['filter' => $filter]);
    }
}
