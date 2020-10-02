<?php

use Bitrix\Main\Localization\Loc;

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
        $this->MODULE_NAME = Loc::getMessage('M_NAME'); //"[BizON] Тестовый модуль";
        $this->MODULE_DESCRIPTION = Loc::getMessage('M_DESC'); //"Тестовый модуль";
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    /**
     * Installation
     */
    public function DoInstall()
    {
        global $APPLICATION, $DOCUMENT_ROOT;
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("INSTALL"), $DOCUMENT_ROOT."/local/modules/itbizon.kalinin/install/step1.php");
    }

    /**
     * Uninstallation
     */
    public function DoUninstall()
    {
        global $APPLICATION, $DOCUMENT_ROOT;
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("UNINSTALL"), $DOCUMENT_ROOT."/local/modules/itbizon.kalinin/install/unstep1.php");
    }

    public static function SayHello($name)
    {
        echo "Hello, " . $name;
    }
}