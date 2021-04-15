<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

use Bitrix\Im\Integration\Intranet\Department;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Itbizon\Finance\AccessRight;
use Itbizon\Finance\Model;

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

/**
 * @param string $user
 * @return array
 */
function getUserIDAndType(string $user): array
{
    $possiblePrefixes = ['U', 'DR'];
    $matches = [];
    if(preg_match('#(' . implode('|', $possiblePrefixes) . ')([0-9]+)#', $user, $matches) === 1
        && !empty($matches[1]) && !empty($matches[2])
    ) {
        array_shift($matches);
        return $matches;
    } else
        return [];
}

/**
 * @param int $id
 * @throws ArgumentException
 * @throws ObjectNotFoundException
 * @throws ObjectPropertyException
 * @throws SystemException
 */
function execRemoveAccessRight(int $id)
{
    $objAccessRight = Model\AccessRightTable::getById($id)->fetchObject();

    if(!$objAccessRight)
        throw new ObjectNotFoundException(Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.AJAX.ACCESS_NOT_FOUND"));

    $result = $objAccessRight->delete();

    if(!$result->isSuccess())
        throw new Exception(implode("</br>", $result->getErrorMessages()));
}

/**
 * @param int $id
 * @throws ArgumentException
 * @throws ObjectException
 */
function execAddAccessRight(int $id)
{
    if(!isset($_REQUEST['USER_ID']))
        throw new ArgumentException(Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.AJAX.INVALID_USER_ID"));

    $user = getUserIDAndType($_REQUEST['USER_ID']);

    if(count($user) < 2)
        throw new ArgumentException(Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.AJAX.INVALID_USER_ID"));

    list($userType, $userId) = $user;
    $userTypeId = $userType == "U" ? Model\AccessRightTable::USER : Model\AccessRightTable::DEPARTMENT;

    if(empty($_REQUEST['ACTION_ACCESS']))
        throw new ArgumentException(Loc::getMessage("ITB_FIN.VAULT_EDIT.ACCESS_RIGHTS.AJAX.INVALID_ACTION"));
    $action = intval($_REQUEST['ACTION_ACCESS']);

    $accessRight = new AccessRight();
    $accessRight->setEntityTypeId(Model\AccessRightTable::ENTITY_VAULT);
    $accessRight->setEntityId($id);
    $accessRight->setAction($action);
    $accessRight->setUserType($userTypeId);
    $accessRight->setUserId($userId);

    $result = $accessRight->save();

    if(!$result->isSuccess()) {
        throw new ObjectException(implode("</br>", $result->getErrorMessages()));
    }
}

/**
 * @param int $id
 * @throws ArgumentException
 * @throws ObjectPropertyException
 * @throws SystemException
 */
function showAccessRights(int $id)
{
    $deps = Department::getList();

    $arrAccessRights = [];
    $listAccessRight = Model\AccessRightTable::getList([
        'filter' => [
            '=ENTITY_TYPE_ID' => Model\AccessRightTable::ENTITY_VAULT,
            '=ENTITY_ID' => $id,
        ]
    ]);
    while ($objAccessRight = $listAccessRight->fetchObject()) {
        $userId = $objAccessRight->getUserId();
        if($objAccessRight->getUserType() == Model\AccessRightTable::DEPARTMENT) {
            $link = "/company/structure.php?set_filter_structure=Y&structure_UF_DEPARTMENT={$userId}";
            $userName = $deps[$userId]["NAME"];
        } elseif($objAccessRight->getUserType() == Model\AccessRightTable::USER) {
            $objUser = UserTable::getByPrimary($userId, [
                'select' => [
                    'ID',
                    'NAME',
                    'LAST_NAME',
                    'EMAIL',
                ]
            ])->fetchObject();
            $link = "/company/personal/user/{$userId}/";

            if(!is_null($objUser->getName()) && !is_null($objUser->getLastName()))
                $userName = "{$objUser->getName()} {$objUser->getLastName()}";
            else
                $userName = $objUser->getEmail();
        } else {
            $link = "";
            $userName = $userId;
        }

        $arrAccessRights[$objAccessRight->getId()] = [
            'ACTION' => Model\AccessRightTable::getActions($objAccessRight->getAction()),
            'USER_TYPE' => Model\AccessRightTable::getUserTypes($objAccessRight->getUserType()),
            'USER_ID' => "<a target='_blank' href='{$link}'>{$userName}</a>",
        ];
    }

    $path = __DIR__ . '/include/accessRights.php';
    if(file_exists($path))
        require($path);
}

/**
 * @param int $id
 * @throws ArgumentException
 * @throws ObjectPropertyException
 * @throws SystemException
 */
function showVaultHistory(int $id)
{
    if(empty($_REQUEST['FROM']) || empty($_REQUEST['TO']))
        throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.REQUEST_INVALID'));

    $historyBegin = DateTime::createFromFormat('d.m.Y', $_REQUEST['FROM'])->setTime(0, 0, 0, 0);
    $historyEnd = DateTime::createFromFormat('d.m.Y', $_REQUEST['TO'])->setTime(23, 59, 59, 999999);

    // Get vault
    $vault = Model\VaultTable::getByPrimary($id)->fetchObject();
    if(!$vault)
        throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.VAULT_INVALID'));

    /**
     * Переменная используется в подключаемом файле
     */
    $records = $vault->loadHistory($historyBegin, $historyEnd);
    $path = __DIR__ . '/include/vaultHistory.php';

    if(file_exists($path))
        require($path);
}

try {
    if(!Loader::includeModule('itbizon.finance'))
        throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.INCLUDE_FIN'));
    if(!Loader::includeModule('im'))
        throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.INCLUDE_IM'));

    if($_SERVER['REQUEST_METHOD'] === "POST") {

        if(empty($_REQUEST['ID']))
            throw new Exception(Loc::getMessage('ITB_FIN.VAULT_EDIT.ERROR.ID_INVALID'));
        $id = intval($_REQUEST['ID']);

        ob_start();

        if($_REQUEST['ACTION'] == "GET_ACCESS") {
            showAccessRights($id);
        } elseif($_REQUEST['ACTION'] == "ADD_ACCESS") {
            execAddAccessRight($id);
        } elseif($_REQUEST['ACTION'] == "REMOVE_ACCESS") {
            execRemoveAccessRight($id);
        } else {
            showVaultHistory($id);
        }

        $html = ob_get_clean();
        answer(Loc::getMessage('ITB_FIN.VAULT_EDIT.ANSWER.SUCCESS'), $html);
    }
} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}
answer("", null, 500); // Ответ не был выдан
