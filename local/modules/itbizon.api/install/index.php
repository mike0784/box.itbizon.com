<?php

use Bitrix\Main\ModuleManager;

class itbizon_api extends CModule
{
    /**
     * itbizon_api constructor.
     */
    public function __construct()
    {
        $this->MODULE_ID = 'itbizon.api';
        $this->MODULE_NAME = '[BizON] API';
        $this->MODULE_DESCRIPTION = 'Тестовое API';
        include(__DIR__.'/version.php');
        /** @var array $arModuleVersion */
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    /**
     * Install
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            CAdminMessage::ShowNote('Успешно установлен');
        } else {
            CAdminMessage::ShowNote('Уже установлен');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Uninstall
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            CAdminMessage::ShowNote('Успешно удален');
        } else {
            CAdminMessage::ShowNote('Уже удален');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Create DB tables
     * @return bool
     */
    public function InstallDB()
    {
        return true;
    }

    /**
     * Remove DB tables
     */
    public function UnInstallDB()
    {
        return true;
    }
}