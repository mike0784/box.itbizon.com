<?php

//namespace Itbizon\Scratch;

use Bitrix\Main\Loader;
use Bizon\Main\Tasks\CheckItem;

use Itbizon\Scratch\Box;
use Itbizon\Scratch\Thing;

use Itbizon\Scratch\Model\BoxTable;
use Itbizon\Scratch\Model\ThingTable;

//use Itbizon\Scratch\Model\Box;
//use Itbizon\Scratch\Model\Thing;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Scratch");

try
{
    if(!Loader::includeModule('itbizon.scratch'))
        throw new Exception('Ошибка подключения модуля itbizon.scratch');

    $r = BoxTable::getList();
    $d = $r->fetchAll();
    echo "<br>Box<pre>";     print_r($d);     echo "</pre>\n";

    /* init record
    BoxTable::add([
        'TITLE' => 'box title',
        'AMOUNT' => 0,
        'COUNT' => 0,
        'COMMENT' => 'box comment',
    ]);
    // */

    /* init record
    ThingTable::add([
        'BOX_ID' => 3,
        'NAME' => 'my thing',
        'DESCRIPTION' => '12345',
        'VALUE' => 1,
        'COMMENT' => 'thing comment',
    ]);
    // */

    $r = ThingTable::getList();
    $d = $r->fetchAll();
    echo "<br>Thing<pre>";     print_r($d);     echo "</pre>\n";

    // increase item value



    /* init record
    ThingTable::Add([
        'BOX_ID' => 1,
        'NAME' => 'thing name',
        'DECRIPTION' => 'thing descr',

        'VALUE' => 1,
        'IS_TRASH' => 'N',
        'COMMENT' => 'thing comment',
    ]);
    // */


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


    /*
    $output=null;
    $retval=null;
    //exec('whoami', $output, $retval);
    exec('cd /home/bitrix/www/bitrix && php bitrix.php orm:annotate -m itbizon.scratch', $output, $retval);
    echo "Returned with status $retval and output:\n";
    print_r($output);
    // */

    //phpinfo();

    /*
    echo "<br>-----<br>";
    echo "<br><pre>";     print_r($USER);     echo "</pre>\n";
    echo $USER->GetId();
    echo "<br>-----<br>";
    // */

    /*
    echo "<br>-----<br>";
    echo "<br><pre>";     print_r(get_declared_classes());     echo "</pre>\n";
    echo "<br>-----<br>";
    // */


}
catch(Exception $e)
{
    echo '<p class="alert alert-danger">'.$e->getMessage().'</p>';
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
