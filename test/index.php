<?php

use Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

class MyClass {
    public string $name;
}
$object = new MyClass();
echo $object->name;

try
{


    $object = new MyClass();
    echo $object->name;
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");