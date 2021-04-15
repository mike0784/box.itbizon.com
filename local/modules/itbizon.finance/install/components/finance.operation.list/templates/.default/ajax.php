<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Data\Result;
use Itbizon\Finance\Model;
use Itbizon\Finance\Permission;

Loc::loadMessages(__FILE__);

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
        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_LIST.ERROR.INCLUDE_FIN'));

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['id']) && isset($_REQUEST['action'])) {
        // Get id
        $id = intval($_REQUEST['id']);
        $currentUser = CurrentUser::get();
        if (!$currentUser || !$currentUser->getId())
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_LIST.ERROR.ACCESS_DENIED'));

        // Get Operation
        $operation = Model\OperationTable::getByPrimary($id)->fetchObject();
        if (!$operation)
            throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_LIST.ERROR.INVALID_CATEGORY'));

        // Remove Operation
        if ($_REQUEST['action'] == 'remove') {
            if (!Permission::getInstance()->isAllowOperationDelete($operation))
                throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_LIST.ERROR.ACCESS_DENY'));
            /**
             * @var Result $result
             */
            $result = $operation->delete();
            if (!$result->isSuccess())
                throw new Exception(implode(" | ", $result->getErrorMessages()));
        } elseif ($_REQUEST['action'] == 'accept') // Confirm Operation
            $operation->confirm($currentUser->getId());
        elseif ($_REQUEST['action'] == 'decline') // Decline Operation
            $operation->decline($currentUser->getId());
        elseif ($_REQUEST['action'] == 'cancel') // Cancel Operation
            $operation->rollback($currentUser->getId());

        answer(Loc::getMessage('ITB_FIN.OPERATION_LIST.ANSWER.SUCCESS'), null);
    }
} catch (Exception $e) {
    answer($e->getMessage().' '.$e->getTraceAsString(), null, 500);
}