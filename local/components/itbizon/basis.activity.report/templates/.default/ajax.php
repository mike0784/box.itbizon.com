<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Crm\DealTable;
use Bitrix\Crm\LeadTable;
use \Bitrix\Main\Loader;
use Itbizon\Basis\Utils\HelperActivityReport;

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
        $customFieldLead = trim(COption::GetOptionString("itbizon.basis", "date_last_activity_lead"));
        $filterLead = ['=ASSIGNED_BY_ID' => $userId];
        $result = LeadTable::getList([
            'select' => ['ID', 'TITLE', $customFieldLead],
            'filter' => HelperActivityReport::prepareFilterDealOrLead([$userId], $customFieldLead, $filter)
        ]);
        while ($row = $result->fetch()) {
            $data[] = [
                'ID' => $row['ID'],
                'TITLE' => $row['TITLE'],
                'LINK' => "/crm/lead/details/{$row['ID']}/",
                'DATE' => $row[$customFieldLead],
            ];
        }

        answer('', getHtml($data));
    }
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $cmd === 'getDealsPopup') {
        $data = [];
        $customFieldDeal = trim(COption::GetOptionString("itbizon.basis", "date_last_activity_deal"));
        $result = DealTable::getList([
            'select' => ['ID', 'TITLE', $customFieldDeal],
            'filter' => HelperActivityReport::prepareFilterDealOrLead([$userId], $customFieldDeal, $filter)
        ]);

        while ($row = $result->fetch()) {
            $data[] = [
                'ID' => $row['ID'],
                'TITLE' => $row['TITLE'],
                'LINK' => "/crm/deal/details/{$row['ID']}/",
                'DATE' => $row[$customFieldDeal],
            ];
        }
        answer('', getHtml($data));
    }
    throw new Exception('Команда не найдена');

} catch (Exception $e) {
    answer($e->getMessage(), [], 500);
}