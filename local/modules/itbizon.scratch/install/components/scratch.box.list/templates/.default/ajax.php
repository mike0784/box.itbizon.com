<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Scratch\Model;

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
    if (!Loader::includeModule('itbizon.scratch'))
        throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_LIST.ERROR.INCLUDE'));

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['remove'])) {

        // Get id
        $id = intval($_REQUEST['remove']);

        // Get Box
        $box = Model\BoxTable::getByPrimary($id)->fetchObject();
        if (!$box)
            throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_LIST.ERROR.INVALID_BOX'));
        //if (!Permission::getInstance()->isAllowCategoryDelete($category))
        //    throw new Exception(Loc::getMessage('ITB_SCRATCH.BOX_LIST.ERROR.ACCESS_DENY'));

        // Remove Box
        $result = $box->delete();
        if (!$result->isSuccess())
            throw new Exception(implode(" | ", $result->getErrorMessages()));

        answer(Loc::getMessage('ITB_SCRATCH.BOX_LIST.ANSWER.SUCCESS'), null);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
