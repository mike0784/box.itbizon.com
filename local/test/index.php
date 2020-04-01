<?php

use Bitrix\Main\Loader;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("12345");

if (CModule::IncludeModule('itbizon.template')) {
   echo \Itbizon\Template\TestClass::addNewRow();
} else {
    print_r('error');
}

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>