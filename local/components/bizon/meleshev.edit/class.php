<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Meleshev\AutoTable;
use Itbizon\Meleshev\Manager;
use Itbizon\Meleshev\ShopTable;
use Bitrix\Main\UI\PageNavigation;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class EditClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.meleshev')) {
            return false;
        }

        $post = $_POST;
        if (isset($post['NEW_CAR'])) {
            $this->addOrUpdateCar($post);
        }
        elseif (isset($post['CAR'])) {
            $this->addOrUpdateCar($post);
        }
        elseif (isset($post['ID'])) {
            $this->addOrUpdateShop($post);
        }

        $id = $this->arParams['ID'];

        if ($id > 0) {
            $shopAndCars = manager::getShopWithCars($id);
            $shop = $shopAndCars['shop'];
            $cars = $shopAndCars['cars'];

        } else {
            $shop = [
                "ID" => 0,
                "TITLE" => "",
                "AMOUNT" => 0,
                "COUNT" => 0,
                "COMMENT" => ""
            ];
            $cars = [];
        }

        $users = UserTable::getList()->fetchAll();

        $this->arResult = [
            'SHOP' => $shop,
            "USERS" => $users,
            'CARS' => $cars
        ];

        $this->IncludeComponentTemplate();
        return true;
    }

    public function addOrUpdateShop($post)
    {
        $id = $post['ID'];
        if ($id == 0) {
            unset($post['ID']);
            $result = ShopTable::add($post);
            if (!$result->isSuccess()) {
                throw new Exception("Не удалось сохранить данные");
            }
        }

        if ($id > 0) {
            $result = ShopTable::update($post['ID'], $post);
            if (!$result->isSuccess()) {
                throw new Exception("Не удалось обновить данные");
            }
        }
        $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $regexp = '/.*\:\d+(.*)edit\/\d/';
        preg_match($regexp, $currentUrl, $matches);
        LocalRedirect($matches[1]);
    }

    public function addOrUpdateCar($post)
    {
        $post['IS_USED'] = isset($post['IS_USED']) ? 'Y' : 'N';
        if (isset($post['NEW_CAR'])) {
            unset($post['NEW_CAR']);
            $result = AutoTable::add($post);
            if (!$result->isSuccess()) {
                $messages = $result->getErrorMessages();
                throw new Exception(implode('\r\n', $messages));
            }
        }

        if (isset($post['CAR'])) {
            unset($post['CAR']);
            $result = AutoTable::update($post['ID'], $post);
            if (!$result->isSuccess()) {
                throw new Exception("Не удалось обновить данные");
            }
        }

    }

}