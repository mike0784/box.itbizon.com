<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$APPLICATION->SetTitle(Loc::getMessage("ITB_FIN.PAGE.VAULT")); ?><? $APPLICATION->IncludeComponent(
    "itbizon:finance.vault.router",
    "",
    array(
        "SEF_FOLDER" => "/finance/vault/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => array("add" => "add/", "groupadd" => "groupadd/", "edit" => "edit/#ID#/", "list" => "/")
    )
); ?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>