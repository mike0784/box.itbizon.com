<?php

use Bitrix\Main\UserTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class ComponentClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!\Bitrix\Main\Loader::includeModule('itbizon.template')) {
            return false;
        }

        $arDefaultUrlTemplates404 = [
            'index' => 'index.php',
            'create' => 'create/',
        ];

        $fines = \Itbizon\Template\SystemFines\Model\FinesTable::getList();

        $this->arResult = [
            'FINES' => $fines
        ];

        $this->IncludeComponentTemplate();
        return true;
    }
}
