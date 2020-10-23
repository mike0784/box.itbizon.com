<?php

use Bitrix\Main\Loader;
use Itbizon\Kalinin\Manager;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class EditClass extends CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.kalinin'))
            throw new Exception('Ошибка подключения модуля itbizon.kalinin');

        $path = $this->GetPath() . '/templates/.default/ajax.php';
        $homePath = '/local/test/kalinin/';


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
            'ships' => $ships,
            'path' => $path,
            'home_path' => $homePath
        ];

        $this->IncludeComponentTemplate();
        return true;
    }
}