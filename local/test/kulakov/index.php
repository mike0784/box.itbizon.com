<?php
use \Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Проверка тестового модуля");

try
{
    if(!Loader::includeModule('itbizon.kulakov'))
        throw new Exception('Ошибка подключения модуля itbizon.kulakov');
}
catch(Exception $e)
{
    echo $e->getMessage();
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");