<?php

use \Bitrix\Main\Loader;
use Bitrix\Main\UI\Extension;
use \Itbizon\Kalinin\Station;
use \Itbizon\Kalinin\Ship;

global $APPLICATION;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
Extension::load('ui.bootstrap4');
$APPLICATION->SetTitle("Проверка тестового модуля");

if(Loader::includeModule('itbizon.kalinin'))
{
    $APPLICATION->IncludeComponent(
        "itbizon.kalinin:station.index",
        "",
        []
    );
}


require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
