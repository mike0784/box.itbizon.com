<?php

use Bitrix\Main\UI\Extension;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("");

$APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");
Extension::load('ui.bootstrap4');

$APPLICATION->IncludeComponent(
    "itbizon.kulakov:router",
    "",
    Array(
        "SEF_FOLDER" => "/local/test_component/",
        "SEF_MODE" => "Y"
    )
);

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");