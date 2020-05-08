<?php

use \Bitrix\Main\Loader;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Модель НМ. Тест");

try {
    if (!Loader::includeModule('itbizon.meleshev'))
        throw new Exception('error load module itbizon.meleshev');

} catch (Exception $ex) {
    echo $ex->getMessage();
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
