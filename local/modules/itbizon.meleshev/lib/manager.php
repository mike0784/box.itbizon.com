<?php

use Itbizon\Meleshev\AutoTable;
use Itbizon\Meleshev\ShopTable;

class Manager
{
    public function createShop()
    {

    }

    public static function addCar($data)
    {
        $result = AutoTable::add($data);
        if (!$result->isSuccess()) {
            throw new Exception("Ошибка: машина не добавлена.");
        }
    }

    public function deleteCar($carId)
    {
        $result = AutoTable::delete($carId);
        if (!$result->isSuccess()) {
            throw new Exception("Ошибка: машина не добавлена.");
        }
    }

    public function deleteShop($shopId)
    {
//        $res = ShopTable::getAllAuto($shopId);
//        foreach ($res as $auto) {
//            $auto->delete();
//        }
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

    public function getShopWithCars($shopId)
    {
        $shop = ShopTable::getById($shopId)->fetch();
        $cars = ShopTable::getAllAuto($shop->id);
        return [$shop, $cars];
    }
}
