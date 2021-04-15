<?php

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/itbizon.finance/include.php");

$sSectionName = Loc::getMessage("ITB_FIN.MODULE_NAME");
$arDirProperties = [];
