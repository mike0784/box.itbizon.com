<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Itbizon\Meleshev\ShopTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ListClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.meleshev')) {
            return false;
        }

        $shops = ShopTable::getList(['select' => ['*']])->fetchAll();

        $users = UserTable::getList()->fetchAll();
        //$path = $this->GetPath() . '/templates/.default/ajax.php';
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
}