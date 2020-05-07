<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class itbizon_meleshev extends CModule
{
    public $MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__.'/version.php');
        if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID          = 'itbizon.meleshev';
        $this->MODULE_NAME        = '[BizON] Тестовый модуль НМ';
        $this->MODULE_DESCRIPTION = 'Обычный тестовый модуль';
    }

    /**
     *
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            CAdminMessage::ShowNote('Модуль установлен');
        } else {
            CAdminMessage::ShowNote('Ошибка установки модуля');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     *
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            CAdminMessage::ShowNote('Модуль удален');
        } else {
            CAdminMessage::ShowNote('Ошибка удаления модуля');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}