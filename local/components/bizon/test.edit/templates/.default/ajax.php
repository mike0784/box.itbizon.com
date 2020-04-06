<?php

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer($message, $data = null)
{
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
    if (!\Bitrix\Main\Loader::includeModule('itbizon.template')) {
        http_response_code(500);
        answer('Модель не подключен');
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $id = $_REQUEST['ID'];
        unset($_REQUEST['ID']);
        $request = $_REQUEST;

        $fine = \Itbizon\Template\SystemFines\Model\FinesTable::update($id, $request);

        if (!$fine->isSuccess()) {
            http_response_code(400);
            answer('invalid response', convertErrorToArray($fine));
        }

        http_response_code(204);
        answer('Success');
    }

} catch (Exception $e) {
    http_response_code(500);
    answer($e->getMessage());
}