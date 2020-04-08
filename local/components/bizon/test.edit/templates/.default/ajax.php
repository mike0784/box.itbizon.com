<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Template\SystemFines\Entities\Fines;
use Itbizon\Template\SystemFines\EntityManager;
use \Itbizon\Template\SystemFines\Model\FinesTable;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer(string $message, $data = null, int $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

function convertErrorToArray($fine)
{
    $errors = [];
    foreach ($fine->getErrors() as $error) {
        $errors[$error->getField()->getName()] = $error->getMessage();
    }
    return $errors;
}

try {
    if (!Loader::includeModule('itbizon.template')) {
        answer('Модель не подключен', null, 500);
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['ID'])) {
        $users = UserTable::getList()->fetchAll();
        $fine = EntityManager::getRepository(Fines::class)->findById($_REQUEST['ID']);
        $path = $APPLICATION->GetCurDir() . 'ajax.php';
        ob_start();
        require(__DIR__ . '/include/popup.php');
        $html = ob_get_clean();
        answer('Success', $html);
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $id = $_REQUEST['ID'];
        if (empty($id)) {
            answer('invalid response', [], 500);
        }
        unset($_REQUEST['ID']);
        $request = $_REQUEST;

        $fine = FinesTable::update($id, $request);

        if (!$fine->isSuccess()) {
            answer('invalid response', convertErrorToArray($fine), 400);
        }

        answer('Success', null, 204);
    }
} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}