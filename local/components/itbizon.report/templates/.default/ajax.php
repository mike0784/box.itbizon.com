<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;
const DATE_FORMAT_OUTPUT = "Y-m-d";
const DATE_FORMAT_FILTER = "d.m.Y 00:00:00";


use Bitrix\Main\Loader;

function answer($message, $data = null, int $code = 200 )
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {
    if (!Loader::includeModule("tasks"))
        throw new Exception("Модуль tasks не найден");

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['taskDone']))
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        $strDateFrom = $request->getCookieRaw("REPORT_FROM");
        $strDateTo = $request->getCookieRaw("REPORT_TO");

        $dateFrom = DateTime::createFromFormat(DATE_FORMAT_OUTPUT, $strDateFrom);
        $dateTo = DateTime::createFromFormat(DATE_FORMAT_OUTPUT, $strDateTo);

        var_dump($request->getCookieRawList());
        var_dump($strDateFrom);

        $id = $_REQUEST['taskDone'];
        if($id)
        {
            $tasksList = CTasks::GetList([],
                [
                    '::LOGIC'       => 'AND',
                    'CREATED_BY'    => $arItem['ID'],
                    '>=DEADLINE'    => $dateFrom->format(DATE_FORMAT_FILTER),
                    '<=DEADLINE'    => $dateTo->format(DATE_FORMAT_FILTER),
                    '=REAL_STATUS'  => '5'
                ],
                [
                    "ID",
                    "TITLE",
                    "CREATED_BY",
                    "DESCRIPTION",
                    "DATE_START",
                    "CLOSED_DATE",
                    "DEADLINE",
                    "REAL_STATUS"
                ]
            );
        }

        ob_start();
        require(__DIR__ . '/include/taskTable.php');
        $html = ob_get_clean();
        answer('Success', $html);

    }

}
catch (Exception $e)
{
    answer($e->getMessage(), null, 500);
}

