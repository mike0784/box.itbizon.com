<?php


require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("Проверка тестового модуля");


if (CModule::IncludeModule('itbizon.kalinin'))
{
    itbizon_kalinin::SayHello("BizON");
}


require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
