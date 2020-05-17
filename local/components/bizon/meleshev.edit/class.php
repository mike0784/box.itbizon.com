<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Meleshev\AutoTable;
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
        $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        var_dump($currentUrl);

        $post = $_POST;

        if (isset($post['ID'])) {
            $this->addOrUpdateShop($post);
        }

        $id = $this->arParams['ID'];

        if ($id > 0) {
            $shop = ShopTable::getById($id)->fetch();
//            $carsCount = ShopTable::getCountOfAllAuto($id);
//
//            $nav = new PageNavigation("nav-more-cars");
//            $nav->allowAllRecords(true)
//                ->setPageSize(5)
//                ->initFromUri();
//
//            $carsList = AutoTable::getList([
//                'filter'      => ['=SHOP_ID' => $id],
//                'count_total' => true,
//                'offset'      => $nav->getOffset(),
//                'limit'       => $nav->getLimit()
//            ]);
//
//            $nav->setRecordCount($carsCount);
//
//            while ($cars = $carsList->fetch())
//            {
//            }
        } else {
            $shop = [
                "ID" => 0,
                "TITLE" => "",
                "AMOUNT" => 0,
                "COUNT" => 0,
                "COMMENT" => ""
            ];
        }

        $users = UserTable::getList()->fetchAll();

        $this->arResult = [
            'SHOP' => $shop,
            "USERS" => $users
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

}