<?php

use Bitrix\Main\UI\Extension;
use Itbizon\Template\TestClass;

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/header.php");
$APPLICATION->SetTitle("");

Extension::load('ui.bootstrap4');

echo "Hello World";

require($_SERVER['DOCUMENT_ROOT'] . "/bitrix/footer.php");
