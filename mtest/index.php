<?php

use \Bitrix\Main\Loader;
use Itbizon\Meleshev\AutoTable;
use Itbizon\Meleshev\ShopTable;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.meleshev/lib/Models/auto.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.meleshev/lib/Models/shop.php');

$APPLICATION->SetTitle("Модель НМ. Тест");

try {
    if (!Loader::includeModule('itbizon.meleshev'))
        throw new Exception('error load module itbizon.meleshev');

} catch (Exception $ex) {
    echo $ex->getMessage();
}

$shopId = 1;
$count = ShopTable::getCountOfAllAuto($shopId);
$shop = ShopTable::getById($shopId)->fetch();
$parameters = [
    'select' => ['*'],
    'filter' => ['=SHOP_ID' => $shopId]
];
var_dump($shop);
echo "<br />In show with id = 0 all cars is = $count with AMOUNT = {$shop["AMOUNT"]} and COUNT = {$shop["COUNT"]}";

$cars = AutoTable::getList($parameters)->fetchAll();
foreach ($cars as $car) {

    echo "<br />Машина №{$car["ID"]} с маркой {$car["MARK"]}";
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
