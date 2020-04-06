<?php

use Bitrix\Main\UI\Extension;
use Itbizon\Template\TestClass;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("");

Extension::load('ui.bootstrap4');

$APPLICATION->IncludeComponent(
    "bizon:test.router",
    "",
    Array(
        "SEF_FOLDER" => "/local/test/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => Array("edit"=>"#ID#/edit/","index"=>"")
    )
);

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>