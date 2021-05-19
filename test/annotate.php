<?php

use Bitrix\Main\Loader;
use Bizon\Main\Tasks\CheckItem;

use Itbizon\Scratch\Model\BoxTable;
use Itbizon\Scratch\Model\ThingTable;


require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Annotate");

try
{
    //if(!Loader::includeModule('itbizon.scratch'))
    //    throw new Exception('Ошибка подключения модуля itbizon.scratch');

    //*
    $output=null;
    $retval=null;
    exec('cd /home/bitrix/www/bitrix && php bitrix.php orm:annotate -m itbizon.scratch', $output, $retval);
    echo "Returned with status $retval and output:\n";
    echo "<br><pre>";
    print_r($output);
    echo "</pre>\n";
    // */

}
catch(Exception $e)
{
    echo '<p class="alert alert-danger">'.$e->getMessage().'</p>';
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
