<?php

use \Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use \Itbizon\Template\SystemFines\Entities\Fines;
use \Itbizon\Template\SystemFines\EntityManager;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class TableClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.template')) {
            return false;
        }

        $fines = EntityManager::getRepository(Fines::class)->findAll();
        $users = UserTable::getList()->fetchAll();
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
