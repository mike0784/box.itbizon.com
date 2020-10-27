<?php

use \Bitrix\Main\Loader;
use \Bitrix\Main\UserTable;
use \Itbizon\Kalinin\Manager;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
header('Content-Type: application/json');

global $APPLICATION;

function answer($message, $data = null, int $code = 200 )
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
            Manager::recycleShip($id);

        answer('Success', $id);
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET") {

        $path = $APPLICATION->GetCurDir() . 'ajax.php';
        $StationID = $_REQUEST['StationID'];
        $ShipID = 0;

        ob_start();
        require(__DIR__ . '/include/shipPopup.php');
        $html = ob_get_clean();
        answer('Success', $html);

    }

    if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_REQUEST['station'])) {
        if(!isset($_REQUEST['NAME']))
            answer('Name empty', null, 400);

        $id = intval($_REQUEST['station']);

        if($id === 0) {
            $station = Manager::createStation([
                'NAME' => $_REQUEST["NAME"],
                'COMMENT' => (isset($_REQUEST['COMMENT']) ? $_REQUEST['COMMENT'] : "")
            ]);
            $id = $station->get("ID");
        } else {
            Manager::updateStation(
                $id, [
                'NAME' => $_REQUEST['NAME'],
                'COMMENT' => (isset($_REQUEST['COMMENT']) ? $_REQUEST['COMMENT'] : "")
            ]);
        }

        answer('Success', $id, 201);
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {

        $id = intval($_REQUEST['editShip']);
        $ship = null;
        $invalid = [];

        if(empty($_REQUEST['NAME'])) $invalid['NAME'] = "Заполните поле";

        if(empty($_REQUEST['VALUE'])) $invalid['VALUE'] = "Заполните поле";
        if(!is_numeric($_REQUEST['VALUE'])) $invalid['VALUE'] = "Введите число";

        if(empty($_REQUEST['MATERIALS'])) $invalid['MATERIALS'] = "Заполните поле";

        if(!empty($invalid))
            answer('Success', $invalid, 400);

        try {
            if($id === 0) {
                $ship = Manager::createShip([
                    'STATION_ID'    => $_REQUEST['STATION_ID'],
                    'NAME'         => $_REQUEST['NAME'],
                    'VALUE'         => floatval($_REQUEST['VALUE']) * 100,
                    'MATERIALS'         => $_REQUEST['MATERIALS'],
                    'COMMENT'       => $_REQUEST['COMMENT'],
                ]);

            }
        } catch (Exception $e) {
            answer("Here is error", null, 500);
        }

        answer('Success', $ship);
    }

} catch (Exception $e) {
    answer($e->getMessage(), null, 500);
}