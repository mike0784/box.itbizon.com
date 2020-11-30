<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use \Bitrix\Crm\ActivityTable;
use \Bitrix\Crm\DealTable;
use \Bitrix\Crm\LeadTable;
use \Bitrix\Crm\Timeline\Entity\TimelineTable;
use \Bitrix\Main\Loader;

header('Content-Type: application/json');

function answer($message, $data = [], $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

function getHtml($data)
{
    $html = '';
    ob_start();
    require(__DIR__ . '/include/leads-deals-table.php');
    $html = ob_get_clean();

    return $html;
}

try {
    if (!Loader::includeModule('itbizon.basis')) {
        throw new Exception('Ошибка подключения модуля itbizon.basis');
    }
    if (!Loader::includeModule('crm')) {
        throw new Exception('Ошибка подключения модуля crm');
    }

    $request = $_REQUEST;
    $cmd = strval($request['cmd']);
    $userId = intval($request['userId']);
    $filter = (isset($request['filter']) && is_array($request['filter'])) ? $request['filter'] : [];

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $cmd === 'getLeadsPopup') {
        $data = [];

        $leads = [];
        $activities = ActivityTable::getList([
            'select' => ['OWNER_ID', 'OWNER_TYPE_ID', 'CREATED', 'AUTHOR_ID'],
            'filter' => array_merge([
                '=OWNER_TYPE_ID' => CCrmOwnerType::Lead,
                '=AUTHOR_ID' => $userId,
            ], $filter)
        ]);

        while ($row = $activities->fetch()) {
            $leads[$row['OWNER_ID']][] = $row['CREATED'];
        }

        $timeLineTable = TimelineTable::getList([
            'select' => ['CREATED', 'TYPE_ID', 'BINDINGS'],
            'filter' => array_merge([
                '=TYPE_ID' => 7, //Тип комментария
                '=AUTHOR_ID' => $userId,
                '=CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_TYPE_ID' => CCrmOwnerType::Lead
            ], $filter)
        ]);

        while ($row = $timeLineTable->fetch()) {
            $leads[$row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID']][] = $row['CREATED'];
        }

        $leadsIds = array_unique(array_keys($leads));
        $result = LeadTable::getList([
            'select' => ['ID', 'TITLE'],
            'filter' => [
                '=ID' => $leadsIds,
            ]
        ]);
        while ($row = $result->fetch()) {
            $activityDate = null;
            if (isset($leads[$row['ID']]) && !empty($leads[$row['ID']])) {
                $activityDateCreates = $leads[$row['ID']];
                if (count($activityDateCreates) > 1) {
                    $activityDate = max($activityDateCreates);
                } else {
                    $activityDate = array_values($activityDateCreates)[0];
                }
            }
            $data[] = [
                'ID' => $row['ID'],
                'TITLE' => $row['TITLE'],
                'LINK' => "/crm/lead/details/{$row['ID']}/",
                'DATE' => $activityDate,
            ];
        }
        answer('', getHtml($data));
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $cmd === 'getDealsPopup') {
        $data = [];

        $deals = [];
        $activities = ActivityTable::getList([
            'select' => ['OWNER_ID', 'OWNER_TYPE_ID', 'CREATED', 'AUTHOR_ID'],
            'filter' => array_merge([
                '=OWNER_TYPE_ID' => CCrmOwnerType::Deal,
                '=AUTHOR_ID' => $userId,
            ], $filter)
        ]);

        while ($row = $activities->fetch()) {
            $deals[$row['OWNER_ID']][] = $row['CREATED'];
        }

        $timeLineTable = TimelineTable::getList([
            'select' => ['CREATED', 'TYPE_ID', 'BINDINGS'],
            'filter' => array_merge([
                '=TYPE_ID' => 7, //Тип комментария
                '=AUTHOR_ID' => $userId,
                '=CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_TYPE_ID' => CCrmOwnerType::Deal
            ], $filter)
        ]);

        while ($row = $timeLineTable->fetch()) {
            $deals[$row['CRM_TIMELINE_ENTITY_TIMELINE_BINDINGS_ENTITY_ID']][] = $row['CREATED'];
        }

        $dealsIds = array_unique(array_keys($deals));
        $result = DealTable::getList([
            'select' => ['ID', 'TITLE'],
            'filter' => [
                '=ID' => $dealsIds,
            ]
        ]);
        while ($row = $result->fetch()) {
            $activityDate = null;
            if (isset($deals[$row['ID']]) && !empty($deals[$row['ID']])) {
                $activityDateCreates = $deals[$row['ID']];
                if (count($activityDateCreates) > 1) {
                    $activityDate = max($activityDateCreates);
                } else {
                    $activityDate = array_values($activityDateCreates)[0];
                }
            }
            $data[] = [
                'ID' => $row['ID'],
                'TITLE' => $row['TITLE'],
                'LINK' => "/crm/deal/details/{$row['ID']}/",
                'DATE' => $activityDate,
            ];
        }
        answer('', getHtml($data));
    }
    throw new Exception('Команда не найдена');

} catch (Exception $e) {
    answer($e->getMessage(), [], 500);
}