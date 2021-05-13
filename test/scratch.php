<?php

use Bitrix\Main\Loader;
use Bizon\Main\Tasks\CheckItem;

use Itbizon\Scratch\Model\BoxTable;
use Itbizon\Scratch\Model\ThingTable;


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Scratch");

try
{
    if(!Loader::includeModule('itbizon.scratch'))
        throw new Exception('Ошибка подключения модуля itbizon.scratch');

    /*
    echo "<br>-----<br>";
    $s = BoxTable::getEntity()->compileDbTableStructureDump();
    echo "<br>$s<br>";
    print_r($s);
    // */

    /*
    echo "<br>-----<br>";
    $s = ThingTable::getEntity()->compileDbTableStructureDump();
    echo "<br>$s<br>";
    print_r($s);
    // */



}
catch(Exception $e)
{
    echo '<p class="alert alert-danger">'.$e->getMessage().'</p>';
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
