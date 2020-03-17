<?php
use \Bitrix\Main\Loader;
use \Itbizon\Template\TestClass;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

try
{
    if(!Loader::includeModule('itbizon.tourism'))
        throw new Exception('Ошибка подключения модуля itbizon.tourism');

    $manager = \Itbizon\Tourism\TravelPoint\Manager::getInstance();
    echo '<pre>'.print_r($manager->getRegions(), true).'</pre>';
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");