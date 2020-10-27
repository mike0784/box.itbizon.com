<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Itbizon\Kalinin\Manager;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer($message, $data = null, int $code = 200)
{
    http_response_code($code);
    echo json_encode(['message' => $message, 'data' => $data]);
    die();
}

try {

    if(!Loader::includeModule('itbizon.kalinin'))
        answer('Ошибка подключения модуля itbizon.kalinin', null, 500);

    if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_REQUEST['remove'])) {

        $id = $_REQUEST['remove'];
        if($id !== 0)
            Manager::recycleStation($id);

        answer('Success', ['ID' => $id]);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}