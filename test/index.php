<?php

use Bitrix\Main\Loader;
use Bizon\Main\Tasks\CheckItem;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

try
{
    if(!Loader::includeModule('bizon.main'))
        throw new Exception('Ошибка подключения модуля bizon.main');

    //$list = CheckItem::getList($taskId);
    //CheckItem::onTaskUpdate(1, 2, 3);
    //echo '<pre>'.print_r($list, true).'</pre>';
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");