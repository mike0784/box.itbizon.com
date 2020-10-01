<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class itbizon_kalinin extends CModule {

    /**
     * itbizon_kalinin constructor.
     */
    public function __construct() {
        $this->MODULE_ID = "itbizon.kalinin";
        $this->MODULE_NAME = "[BizON(test)] Тестовый модуль";
        $this->MODULE_DESCRIPTION = "Тестовый модуль";
        $this->MODULE_VERSION = "0.1";
        $this->MODULE_VERSION_DATE = "2020-10-01 13:00:00";
    }

    /**
     *
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID))
        {
            CAdminMessage::ShowNote('Test module is installed');
//            $this->InstallDB();
        }
        else
        {
            CAdminMessage::ShowNote('Installation error');
        }

        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     *
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID))
        {
            CAdminMessage::ShowNote('Test module is deleted');
//            $this->UnInstallDB();
        }
        else
        {
            CAdminMessage::ShowNote('Uninstallation error');
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }


//    /**
//     * @return bool
//     */
//    public function InstallDB()
//    {
//        try
//        {
//            return true;
//        }
//        catch (Exception $e)
//        {
//            return false;
//        }
//    }
//
//    /**
//     * @return bool
//     */
//    public function UnInstallDB()
//    {
//        try
//        {
//            return true;
//        }
//        catch (Exception $e)
//        {
//            return false;
//        }
//    }
}