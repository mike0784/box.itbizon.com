<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$APPLICATION->SetTitle(Loc::getMessage("ITB_FIN.PAGE.INCOME")); ?><? $APPLICATION->IncludeComponent(
    "itbizon:finance.income",
    "",
    []
); ?><?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>