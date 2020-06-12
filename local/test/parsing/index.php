<?php

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("");

$APPLICATION->IncludeComponent(
    "bizon:parsing.excel",
    "",
    []
);

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
?>