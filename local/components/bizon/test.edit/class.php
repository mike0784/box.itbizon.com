<?php

use Bitrix\Main\UserTable;
use \Itbizon\Template\SystemFines\Entities\Fines;
use \Itbizon\Template\SystemFines\EntityManager;
use \Bitrix\Main\Loader;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class EditClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!Loader::includeModule('itbizon.template')) {
            return false;
        }

        if (key_exists('ID', $this->arParams)) {
            $fine = EntityManager::getRepository(Fines::class)->findById((int)$this->arParams['ID']);
//            $fine = FinesTable::getByPrimary($this->arParams['ID'],
//                [
//                    'select' => array( '*',
//                        new ExpressionField('VALUES', 'VALUE/100', array('VALUE')),
//                        'CREATOR_NAME' =>  'CREATOR.NAME',
//                        'TARGET_NAME' =>  'TARGET.NAME',
//                    )
//                ])->fetch();

            $users = UserTable::getList()->fetchAll();
            $path = $this->GetPath() . '/templates/.default/ajax.php';

            $this->arResult = [
                "FINE" => $fine,
                "PATH" => $path,
                "USERS" => $users,
            ];

            $this->IncludeComponentTemplate();
            return true;
        }
        return false;
    }
}
