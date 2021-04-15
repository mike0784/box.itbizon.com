<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\AccessDeniedException;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectNotFoundException;
use Itbizon\Finance\Model\AccessRightTable;

Loc::loadMessages(__FILE__);

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
    if(!CurrentUser::get()->isAdmin())
        throw new AccessDeniedException(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.AJAX.ERRORS.ACCESS_DENIED"));

    if(!Loader::includeModule('itbizon.finance'))
        throw new Exception(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.AJAX.ERRORS.INCLUDE_FINANCE"));

    if($_SERVER['REQUEST_METHOD'] === "POST" && !empty($_REQUEST['ID'])) {

        // Get id
        $id = intval($_REQUEST['ID']);

        // Get object
        $obj = AccessRightTable::getByPrimary($id)->fetchObject();
        if(!$obj)
            throw new ObjectNotFoundException(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.AJAX.ERRORS.OBJ_NOT_FOUND"));

        // Remove object
        $result = $obj->delete();

        if(!$result->isSuccess())
            throw new Exception(implode("</br>", $result->getErrorMessages()));

        answer(Loc::getMessage("ITB_FINANCE.PERMISSION.LIST.AJAX.MESSAGE.SUCCESS"), null);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
