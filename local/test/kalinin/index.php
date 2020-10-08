<?php

use \Bitrix\Main\Loader;

global $APPLICATION;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("Проверка тестового модуля");


if (Loader::IncludeModule('itbizon.kalinin'))
{
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/manager.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/ship.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/station.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/ship.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/station.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/logger/logger.php');

    $man = new \Itbizon\Kalinin\Lib\Model\Manager();
//    $man->createShip('MyShip', 'Plastill', 1000, 1);
    $man->recycleStation(3);
//    $sos = $man->getStationAndShips(2);
}


require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
