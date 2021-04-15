<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$APPLICATION->SetTitle('Главная'); ?><? $APPLICATION->IncludeComponent(
    "itbizon:finance.operation.report",
    "",
    array(

    )
); ?>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>