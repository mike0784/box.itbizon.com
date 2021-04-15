<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$APPLICATION->SetTitle(Loc::getMessage("ITB_FIN.PAGE.ACCESS")); ?><? $APPLICATION->IncludeComponent(
    "itbizon:finance.permission.list",
    "",
    array(
        "SEF_FOLDER" => "/finance/access/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => array("add" => "add/", "edit" => "edit/#ID#/", "list" => "/")
    )
); ?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>