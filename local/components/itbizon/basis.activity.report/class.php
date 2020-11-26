<?php

use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Basis\Utils\HelperActivityReport;

/**
 * Class CITBBasisActivityReport
 */
class CITBBasisActivityReport extends CBitrixComponent
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

            $gridId = 's_activity_report';
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
                    'default' => true
                ],
            ];

            // Converting UI filter to D7 filter
            $filterOption = new \Bitrix\Main\UI\Filter\Options($gridId);
            $filterData = $filterOption->getFilterLogic($filter);

            if (isset($filterData['>=PERIOD']) && isset($filterData['<=PERIOD'])) {
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
                if ($customFieldLead) {
                    $resultLead = LeadTable::getList([
                        'select' => ['ID', 'ASSIGNED_BY_ID'],
                        'filter' => HelperActivityReport::prepareFilterDealOrLead($userIds, $customFieldLead, $filterData)
                    ]);
                    while ($row = $resultLead->fetch()) {
                        $users[$row['ASSIGNED_BY_ID']]['LEADS'][] = $row;
                    }
                }

                if ($customFieldDeal) {
                    $resultLead = DealTable::getList([
                        'select' => ['ID', 'ASSIGNED_BY_ID'],
                        'filter' => HelperActivityReport::prepareFilterDealOrLead($userIds, $customFieldDeal, $filterData)
                    ]);
                    while ($row = $resultLead->fetch()) {
                        $users[$row['ASSIGNED_BY_ID']]['DEALS'][] = $row;
                    }
                }

                $rows = [];
                foreach ($users as $user) {
                    $userId = $user['ID'];
                    $countLeads = count($user['LEADS']);
                    $countDeals = count($user['DEALS']);
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
