<?php

use \Bitrix\Main\Loader;
use \Itbizon\Kalinin\Model\StationTable;
use Itbizon\Kalinin\Manager;

class IndexClass extends CBitrixComponent
{
    public function executeComponent()
    {
        try {
            if (!Loader::includeModule('itbizon.kalinin'))
                throw new Exception('Ошибка подключения модуля itbizon.kalinin');

            if (!empty($_POST))
            {
                foreach ($_POST as $station)
                {
                    $station = $this->getClearPostMultyArray($station);
                    if (isset($station['isDelete']))
                    {
                        Manager::recycleStation($station['ID']);
                    }
                }
            }

            $stations = StationTable::getList(['select' => ['*']])->fetchAll();


            $path = $this->GetPath() . '/templates/.default/ajax.php';
            $homePath = '/local/test/kalinin/';

            $this->arResult = [
                'stations' => $stations,
                'path' => $path,
                'home_path' => $homePath
            ];

            $this->includeComponentTemplate();

        } catch (Exception $e)
        {
            ShowMessage($e->getMessage());
        }
    }

    public function getClearPostMultyArray($post)
    {
        $clearPost = [];
        foreach ($post as $key => $value) {
            $key = str_replace("'", "", $key);
            $clearPost[$key] = $value;
        }

        return $clearPost;
    }
}