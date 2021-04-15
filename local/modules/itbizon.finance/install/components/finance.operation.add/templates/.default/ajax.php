<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model\OperationCategoryTable;
use Itbizon\Finance\Model\VaultTable;

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
        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.INCLUDE_FIN'));

    if (!Loader::IncludeModule('crm'))
        throw new Exception(Loc::getMessage('ITB_FIN.OPERATION_ADD.ERROR.INCLUDE_CRM'));

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['vault'])) {
        $vaults = [];
        $vaultList = VaultTable::getList([
            'select' => [
                'ID',
                'NAME'
            ],
            'filter' => [
                '!=TYPE' => [VaultTable::TYPE_VIRTUAL, VaultTable::TYPE_STOCK]
            ],
            'order' => [
                'NAME' => 'ASC'
            ]
        ]);

        while ($vault = $vaultList->fetchObject()) {
            $vaults[] = [
                'ID' => $vault->getId(),
                'NAME' => $vault->getName(),
            ];
        }

        answer(Loc::getMessage('ITB_FIN.OPERATION_ADD.ANSWER.SUCCESS'), $vaults);
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['categoryFilter'])) {
        $categories = [];
        $categoryList = OperationCategoryTable::getList([
            'select' => [
                'ID',
                'NAME'
            ],
            'filter' => [
                '=' . strval($_REQUEST['categoryFilter']) => 'Y'
            ],
            'order' => [
                'NAME' => 'ASC'
            ]
        ]);
        while ($category = $categoryList->fetchObject()) {
            $categories[] = [
                'ID' => $category->getId(),
                'NAME' => $category->getName(),
            ];
        }

        answer(Loc::getMessage('ITB_FIN.OPERATION_ADD.ANSWER.SUCCESS'), $categories);
    }


} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
