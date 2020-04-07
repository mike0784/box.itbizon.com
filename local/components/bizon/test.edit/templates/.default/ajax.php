<?php

use Bitrix\Main\Loader;
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

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $id = $_REQUEST['ID'];
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