<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model;
use Itbizon\Finance\Permission;

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
        throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_LIST.ERROR.INCLUDE_FIN'));

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['remove'])) {

        // Get id
        $id = intval($_REQUEST['remove']);

        // Get OperationCategory
        $category = Model\OperationCategoryTable::getByPrimary($id)->fetchObject();
        if (!$category)
            throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_LIST.ERROR.INVALID_CATEGORY'));
        if (!Permission::getInstance()->isAllowCategoryDelete($category))
            throw new Exception(Loc::getMessage('ITB_FIN.CATEGORY_LIST.ERROR.ACCESS_DENY'));

        // Remove OperationCategory
        $result = $category->delete();
        if (!$result->isSuccess())
            throw new Exception(implode(" | ", $result->getErrorMessages()));

        answer(Loc::getMessage('ITB_FIN.CATEGORY_LIST.ANSWER.SUCCESS'), null);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
