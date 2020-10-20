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

            $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $regexp = '/.*\:\d+\/(.*)';
            preg_match($regexp, $currentUrl, $matches);

            $this->arResult = [
                'stations' => $stations,
                'DELETE' => $matches[1]
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