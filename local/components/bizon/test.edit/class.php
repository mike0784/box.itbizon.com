<?php

use Bitrix\Main\UserTable;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class EditClass extends \CBitrixComponent
{
    public function executeComponent()
    {
        if (!\Bitrix\Main\Loader::includeModule('itbizon.template')) {
            return false;
        }

        if (key_exists('ID', $this->arParams)) {
            $fine = \Itbizon\Template\SystemFines\Model\FinesTable::getByPrimary($this->arParams['ID'])->fetchObject();
            $creatorName = UserTable::getByPrimary($fine->getCreatorId())->fetchObject();
            $targetName = UserTable::getByPrimary($fine->getTargetId())->fetchObject();
            $creatorUsers = \Bitrix\Main\UserTable::getList();
            $targetUsers = \Bitrix\Main\UserTable::getList();
            $path = $this->GetPath() . '/templates/.default/ajax.php';

            $this->arResult = [
                "ID" => $fine->getId(),
                'TITLE' => $fine->getTitle(),
                'VALUE' => $fine->getValue(),
                'TARGET_ID' => $fine->getTargetId(),
                'TARGET_NAME' => $targetName->getName(),
                'CREATOR_ID' => $fine->getCreatorId(),
                'CREATOR_NAME' => $creatorName->getName(),
                'COMMENT' => $fine->getComment(),
                "PATH" => $path,
                "CREATORS" => $creatorUsers,
                "TARGETS" => $targetUsers
            ];

            $this->IncludeComponentTemplate();
            return true;
        }
        return false;
    }
}
