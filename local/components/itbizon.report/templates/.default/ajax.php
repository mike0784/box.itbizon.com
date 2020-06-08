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

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['TASK_DONE_FUSER']))
    {
        $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

        $strDateFrom = isset($_REQUEST["REPORT_FROM"]) ? $_REQUEST["REPORT_FROM"] : date($dateFormatOutput, strtotime("-7 days"));
        $strDateTo = isset($_REQUEST["REPORT_TO"]) ? $_REQUEST["REPORT_TO"] : date($dateFormatOutput);

        $dateFrom = DateTime::createFromFormat(DATE_FORMAT_OUTPUT, $strDateFrom);
        $dateTo = DateTime::createFromFormat(DATE_FORMAT_OUTPUT, $strDateTo);

        $id = $_REQUEST['TASK_DONE_FUSER'];
        if($id)
        {
            $tasksList = CTasks::GetList([],
                [
                    'CREATED_BY'    => $id,
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

            ob_start();
            require(__DIR__ . '/include/taskTable.php');
            $html = ob_get_clean();
            answer('Success', $html);
        }

        answer('Error', null, 500);
    }

}
catch (Exception $e)
{
    answer($e->getMessage(), null, 500);
}

