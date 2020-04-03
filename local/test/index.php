<?php

use Bitrix\Main\UI\Extension;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("");

Extension::load('ui.bootstrap4');

$APPLICATION->IncludeComponent(
    "bizon:test.index",
    "",
    Array(
        "SEF_FOLDER" => "/local/test/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => Array("create"=>"create/","index"=>"index.php")
    )
);

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>