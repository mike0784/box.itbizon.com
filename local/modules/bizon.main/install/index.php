<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class bizon_main extends CModule
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->MODULE_ID = "bizon.main";
        $this->MODULE_NAME = "[BizON] Работа с агентами";
        $this->MODULE_DESCRIPTION = "";
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "2020-05-20 01:00:00";
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    /**
     * Install
     */
    public function DoInstall()
    {
        if(!ModuleManager::isModuleInstalled($this->MODULE_ID))
            CAdminMessage::ShowNote('Тестовый модуль установлен');
        else
            CAdminMessage::ShowNote('Ошибка установки тестого модуля');

        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Uninstall
     */
    public function DoUninstall()
    {
        if(ModuleManager::isModuleInstalled($this->MODULE_ID))
            CAdminMessage::ShowNote('Тестовый модуль удален');
        else
            CAdminMessage::ShowNote('Ошибка удаления тестого модуля');

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool
     */
    public function InstallDB()
    {
        return true;
    }

    /**
     *
     */
    public function UnInstallDB()
    {
        return true;
    }
}