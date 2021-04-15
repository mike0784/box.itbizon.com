<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$APPLICATION->SetTitle(Loc::getMessage("ITB_FIN.PAGE.STOCK")); ?><? $APPLICATION->IncludeComponent(
    "itbizon:finance.stock.list",
    "",
    array(
        "SEF_FOLDER" => "/finance/stock/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => array("add" => "add/", "edit" => "edit/#ID#/", "list" => "/")
    )
); ?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>