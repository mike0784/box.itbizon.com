<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Itbizon\Template\SystemFines\Model\FinesTable;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer($message, $data = null, int $code = 200)
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

    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_REQUEST['ID'])) {
        $request = $_REQUEST;
        $fine = FinesTable::getByPrimary($request['ID'])->fetchObject();
        if ($fine) {
            $fine->delete();
            answer('Success', null, 204);
        }
        answer('error', null, 404);
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        $users = UserTable::getList()->fetchAll();
        $path = $APPLICATION->GetCurDir() . 'ajax.php';
        ob_start();
        require(__DIR__ . '/include/popup.php');
        $html = ob_get_clean();
        answer('Success', $html);
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $request = $_REQUEST;
        $fine = FinesTable::add($request);

        if (!$fine->isSuccess()) {
            answer('invalid response', convertErrorToArray($fine), 400);
        }
        answer('Success', null, 201);
    }
} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}