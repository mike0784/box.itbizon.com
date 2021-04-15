<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model;

Loc::loadMessages(__FILE__);

/** @var Application $APPLICATION */
global $APPLICATION;

/**
 * @param $message
 * @param null $data
 * @param int $code
 */
function answer($message, $data = null, int $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {
    if (!Loader::includeModule('itbizon.finance'))
        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.INCLUDE_FIN'));

    if (!Loader::IncludeModule('crm'))
        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.INCLUDE_CRM'));

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (empty($_REQUEST['ID']) || empty($_REQUEST['FROM']) || empty($_REQUEST['TO']))
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ERROR.REQUEST_INVALID'));

        $id = intval($_REQUEST['ID']);
        $historyBegin = DateTime::createFromFormat('d.m.Y', $_REQUEST['FROM']);
        $historyEnd = DateTime::createFromFormat('d.m.Y', $_REQUEST['TO']);

        // Get operation
        $records = Model\OperationActionTable::getList([
            'select' => [
                '*',
                'USER.ID',
                'USER.NAME',
                'USER.LAST_NAME',

            ],
            'filter' => [
                'OPERATION_ID' => $id,
                '>=DATE_CREATE' => $historyBegin->format('d.m.Y 00:00:00'),
                '<=DATE_CREATE' => $historyEnd->format('d.m.Y 23:59:59'),
            ]
        ]);

        ob_start();
        require(__DIR__ . '/include/operationHistory.php');
        $html = ob_get_clean();
        answer(Loc::getMessage('ITB_FIN.OPERATION_EDIT.ANSWER.SUCCESS'), $html);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
