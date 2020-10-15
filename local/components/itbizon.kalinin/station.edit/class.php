<?php

use Bitrix\Main\Loader;
use Itbizon\Kalinin\Model\Manager;
use Itbizon\Kalinin\Station;
use Itbizon\Kalinin\Ship;
use Itbizon\Kalinin\Model\ShipTable;
use Itbizon\Kalinin\Model\StationTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class EditClass extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.kalinin'))
            throw new Exception('Ошибка подключения модуля itbizon.kalinin');

        if (isset($_POST['NEW_SHIP'])) {
            $this->addOrUpdateShip($_POST);
        } elseif (isset($_POST['SHIP'])) {
            $this->addOrUpdateShip($_POST);
        } elseif (isset($_POST['ID'])) {
            $this->addOrUpdateStation($_POST);
        }

        if ($this->arParams['ID'] > 0) {
            $stationNShips = Manager::getStationAndShips($this->arParams['ID']);
            $station = $stationNShips['station'];
            $ships = $stationNShips['ships'];
        } else {
            $station = [
                'ID' => 0,
                'NAME' => "",
                'AMOUNT' => 0,
                'COUNT' => 0,
                'COMMENT' => ""
            ];
            $ships = [];
        }

        $this->arResult = [
            'station' => $station,
            'ships' => $ships
        ];

        $this->IncludeComponentTemplate();
        return true;
    }

    public function addOrUpdateStation($post)
    {
        $id = $post['ID'];
        if ($id == 0) {
            unset($post['ID']);

            Manager::createStation($post['NAME'], null, $post['AMOUNT'], $post['COUNT'], $post['COMMENT']);

//            $result = StationTable::add($post);
//            if (!$result->isSuccess()) {
//                throw new Exception("Не удалось сохранить данные");
//            }
        }

        if ($id > 0) {
            Manager::updateStation($post['ID'], $post['NAME'], $post['AMOUNT'], $post['COUNT'], $post['COMMENT']);
//            $result = StationTable::update($post['ID'], $post);
//            if (!$result->isSuccess()) {
//                throw new Exception("Не удалось обновить данные");
//            }
        }
        $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $regexp = '/.*\:\d+(.*)edit\/\d/';
        preg_match($regexp, $currentUrl, $matches);
        LocalRedirect($matches[1]);
    }

    public function addOrUpdateShip($post)
    {
        $post['IS_RELEASED'] = isset($post['IS_RELEASED']) ? 'Y' : 'N';
        if (isset($post['NEW_SHIP'])) {
            Manager::createShip($post['NAME'], $post['MATERIALS'], $post['VALUE'],
                $post['STATION_ID'], null, $post['IS_RELEASED'], $post['COMMENT']);
//            $result = ShipTable::add($post);
//            if (!$result->isSuccess()) {
//                $messages = $result->getErrorMessages();
//                throw new Exception(implode('\r\n', $messages));
//            }
        }

        if (isset($post['SHIP'])) {
            Manager::updateShip($post['ID'], $post['NAME'], $post['MATERIALS'], $post['VALUE'],
                $post['STATION_ID'], $post['IS_RELEASED'], $post['COMMENT']);
//            $result = ShipTable::update($post['ID'], $post);
//            if (!$result->isSuccess()) {
//                throw new Exception("Не удалось обновить данные");
//            }
        }

    }
}