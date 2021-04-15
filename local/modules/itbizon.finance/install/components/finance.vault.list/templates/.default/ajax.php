<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Model;
use Itbizon\Finance\Permission;

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
    if (!Loader::includeModule('itbizon.finance'))
        throw new Exception(Loc::getMessage('ITB_FIN.VAULT_LIST.ERROR.INCLUDE_FIN'));

    if ($_SERVER['REQUEST_METHOD'] === "GET") {

        if(isset($_REQUEST['remove_vault'])) {
            $id = intval($_REQUEST['remove_vault']);

            $vault = Model\VaultTable::getByPrimary($id)->fetchObject();
            if (!$vault)
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_LIST.ERROR.INVALID_VAULT'));

            if(!Permission::getInstance()->isAllowVaultDelete($vault))
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_LIST.ERROR.ACCESS_DENIED'));

            $result = $vault->delete();
            if (!$result->isSuccess())
                throw new Exception(implode(" | ", $result->getErrorMessages()));

            answer(Loc::getMessage('ITB_FIN.VAULT_LIST.ANSWER.SUCCESS'), null);
        } else if(isset($_REQUEST['remove_group'])) {
            $id = intval($_REQUEST['remove_group']);

            $group = Model\VaultGroupTable::getByPrimary($id)->fetchObject();
            if (!$group)
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_LIST.ERROR.INVALID_GROUP'));

            if(!Permission::getInstance()->isAllowVaultGroupDelete($group))
                throw new Exception(Loc::getMessage('ITB_FIN.VAULT_LIST.ERROR.ACCESS_DENIED'));

            $result = $group->delete();
            if (!$result->isSuccess())
                throw new Exception(implode(" | ", $result->getErrorMessages()));

            answer(Loc::getMessage('ITB_FIN.VAULT_LIST.ANSWER.SUCCESS'), null);
        } else {
            throw new Exception('Invalid command');
        }
    }
} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
