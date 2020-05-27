<?php

namespace Itbizon\Meleshev;

use Exception;
use Itbizon\Meleshev\Model\AutoTable;
use Itbizon\Meleshev\Model\ShopTable;

class Manager
{
    public static function createShop($data)
    {
        $result = ShopTable::add($data);

        if (!$result->isSuccess()) {
            throw new Exception("Ошибка: магазин не добавлен.");
        }
    }

    public static function addCar($data)
    {
        $result = AutoTable::add($data);
        if (!$result->isSuccess()) {
            throw new Exception("Ошибка: машина не добавлена.");
        }
    }

    public static function deleteCar($carId)
    {
        $result = AutoTable::delete($carId);
        if (!$result->isSuccess()) {
            throw new Exception("Ошибка: машина не добавлена.");
        }
    }

    public static function deleteShop($shopId)
    {
        $sqlQuery = "DELETE FROM itb_auto WHERE SHOP_ID = $shopId";

        $db = \Bitrix\Main\Application::getConnection();
        $db->queryExecute($sqlQuery);
        if (count(ShopTable::getAllAuto($shopId)) > 0) {
            throw new Exception("Ошибка: машины не были удалены");
        }
        $result = ShopTable::delete($shopId);
        if (!$result->isSuccess()) {
            throw new Exception("Ошибка: магазин не был удален");
        }

    }

    public static function getShopWithCars($shopId)
    {
        $shop = ShopTable::getById($shopId)->fetch();
        $cars = ShopTable::getAllAuto($shopId);
        return ['shop' => $shop, 'cars' => $cars];
    }
}
