<?php

use \Bitrix\Main\Loader;
use \Itbizon\Kalinin\Model\StationTable;
use Itbizon\Kalinin\Lib\Model\Manager;

class IndexClass extends CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.kalinin'))
                throw new Exception('Ошибка подключения модуля itbizon.kalinin');

            $stations = StationTable::getList(['select' => ['*']])->fetchAll();

            $this->arResult = ['stations' => $stations];

            $this->includeComponentTemplate();

        } catch (Exception $e)
        {
            ShowMessage($e->getMessage());
        }
    }
}