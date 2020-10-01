<?php

use \Bitrix\Main\Loader;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("Проверка тестового модуля");

try
{
    if (!Loader::includeModule('itbizon.kalinin'))
        throw new Exception('Ошибка подключения модуля itbizon.kalinin');

}
catch (Exception $e)
{
    echo $e->getMessage();
}

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
