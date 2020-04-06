<?php

use Bitrix\Main\UserTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class TableClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!\Bitrix\Main\Loader::includeModule('itbizon.template')) {
            return false;
        }

        $fines = \Itbizon\Template\SystemFines\Model\FinesTable::getList();
        $users = UserTable::getList();
        $path = $this->GetPath() . '/templates/.default/ajax.php';

        $this->arResult = [
            'FINES' => $fines,
            "USERS" => $users,
            "PATH" => $path
        ];

        $this->IncludeComponentTemplate();
        return true;
    }
}
