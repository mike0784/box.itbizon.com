<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$APPLICATION->SetTitle(Loc::getMessage("ITB_FIN.PAGE.PLANNING")); ?><? $APPLICATION->IncludeComponent(
    "itbizon:finance.period.list",
    "",
    array(
        "SEF_FOLDER" => "/finance/planning/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => array("add" => "add/", "edit" => "edit/#ID#/", "list" => "/", "config" => 'config/')
    )
); ?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>