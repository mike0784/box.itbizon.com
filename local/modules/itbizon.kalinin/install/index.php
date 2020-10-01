<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class itbizon_kalinin extends CModule {

    /**
     * itbizon_kalinin constructor.
     */
    public function __construct() {

        $arModuleVersion = [];
        include(__DIR__ . '/version.php');
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = "itbizon.kalinin";
        $this->MODULE_NAME = "[BizON] Тестовый модуль";
        $this->MODULE_DESCRIPTION = "Тестовый модуль";
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
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