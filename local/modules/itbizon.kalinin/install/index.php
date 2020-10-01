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
        $APPLICATION->IncludeAdminFile("Install itbizon.kalinin", $DOCUMENT_ROOT."/local/modules/itbizon.kalinin/install/step1.php");
    }

    /**
     *
     */
    public function DoUninstall()
    {
        global $APPLICATION, $DOCUMENT_ROOT;
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile("Uninstall itbizon.kalinin", $DOCUMENT_ROOT."/local/modules/itbizon.kalinin/install/unstep1.php");
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