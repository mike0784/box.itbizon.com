<?php

use Bitrix\Main\Loader;
use Itbizon\Kalinin\Manager;
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

            try {
                Manager::createStation($post);
            } catch (\Bitrix\Main\ArgumentException $e) {
                throw new Exception("Не удалось добавить станцию: " . $e->getMessage());
            } catch (\Bitrix\Main\SystemException $e) {
                throw new Exception("Не удалось добавить станцию: " . $e->getMessage());
            }

        }

        if ($id > 0) {
            try {
                Manager::updateStation(intval($id), $post);
            } catch (\Bitrix\Main\ObjectPropertyException $e) {
                throw new Exception("Не удалось обновить данные: " . $e->getMessage());
            } catch (\Bitrix\Main\ArgumentException $e) {
                throw new Exception("Не удалось обновить данные: " . $e->getMessage());
            } catch (\Bitrix\Main\SystemException $e) {
                throw new Exception("Не удалось обновить данные: " . $e->getMessage());
            }
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
            try {
                Manager::createShip($post);
            } catch (\Bitrix\Main\ObjectPropertyException $e) {
                throw new Exception("Не удалось добавить корабль: " . $e->getMessage());
            } catch (\Bitrix\Main\ArgumentException $e) {
                throw new Exception("Не удалось добавить корабль: " . $e->getMessage());
            } catch (\Bitrix\Main\SystemException $e) {
                throw new Exception("Не удалось добавить корабль: " . $e->getMessage());
            }
        }

        if (isset($post['SHIP'])) {
            try {
                Manager::updateShip(intval($post['ID']), $post);
            } catch (\Bitrix\Main\ObjectPropertyException $e) {
                throw new Exception("Не удалось обновить корабль: " . $e->getMessage());
            } catch (\Bitrix\Main\ArgumentException $e) {
                throw new Exception("Не удалось обновить корабль: " . $e->getMessage());
            } catch (\Bitrix\Main\SystemException $e) {
                throw new Exception("Не удалось обновить корабль: " . $e->getMessage());
            }
        }

    }
}