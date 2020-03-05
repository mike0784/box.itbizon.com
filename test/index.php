<?php
use \Bitrix\Main\Loader;
use \Itbizon\Template\TestClass;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

try
{
    if(!Loader::includeModule('itbizon.template'))
        throw new Exception('Ошибка подключения модуля itbizon.template');

    TestClass::test();
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");