<?php
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class itbizon_mike extends CModule
{
    public function  __construct()
    {
        $this -> MODULE_ID = "itbizon.mike";
        include(__DIR__.'/version.php');
        if(is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }
        $this -> MODULE_NAME = Loc::getMessage('ITB_MIKE_MODULE_NAME');
        $this -> MODULE_DESCRIPTION = Loc::getMessage('ITB_MIKE_MODULE_DESCRIPTION');
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            if(!$this->InstallFiles())
                return false;
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_OK'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_ERROR'));
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallFiles();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_UNINSTALL_OK'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_UNINSTALL_ERROR'));
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        return true;
    }

    public function UnInstallDB()
    {
        return true;
    }

    public function InstallEvents()
    {
        return true;
    }

    public function UnInstallEvents()
    {
        return true;
    }

    public function InstallFiles()
    {
        /*if(!CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/itbizon', true, true))
        {
            $GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage('ITB_MIKE_MODULE_ERROR_COPY_FILES'));
            return false;
        }*/
        return true;
    }

    public function UnInstallFiles()
    {
        /*if(!DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/mail/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin"))
        {
            $GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage('ITB_MIKE_MODULE_ERROR_DELETE_FILES'));
            return false;
        }*/
        return true;
    }
}

?>