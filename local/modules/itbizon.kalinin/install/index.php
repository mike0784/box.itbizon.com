<?php

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
        global $APPLICATION, $DOCUMENT_ROOT;
        RegisterModule($this->MODULE_ID);
        CAdminMessage::ShowNote("Module is installed");
    }

    /**
     *
     */
    public function DoUninstall()
    {
        RegisterModule($this->MODULE_ID);
        CAdminMessage::ShowNote("Module is uninstalled");
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