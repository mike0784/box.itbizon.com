<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Meleshev\Manager;
use Itbizon\Meleshev\ShopTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ListClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.meleshev')) {
            return false;
        }
        $post = $_POST;
        if (!empty($post)) {

            foreach ($post as $shop) {
                $shop = $this->getClearPostMultyArray($shop);
                if (isset($shop['isDelete'])) {
                    manager::deleteShop($shop['ID']);
                }
            }
        }

        $shops = ShopTable::getList(['select' => ['*']])->fetchAll();

        $users = UserTable::getList()->fetchAll();
        $currentUrl = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $regexp = '/.*\:\d+\/(.*)';
        preg_match($regexp, $currentUrl, $matches);

        $this->arResult = [
            'SHOPS'  => $shops,
            'USERS'  => $users,
            'DELETE' => $matches[1]
        ];



        $this->IncludeComponentTemplate();
        return true;
    }

    public function getClearPostMultyArray($post)
    {
        $clearPost = [];
        foreach ($post as $key => $value) {
            $key = str_replace("'", "", $key);
            $clearPost[$key] = $value;
        }

        return $clearPost;
    }
}