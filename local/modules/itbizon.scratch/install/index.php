<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class itbizon_scratch extends CModule
{
    public $MODULE_ID = 'itbizon.scratch';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    public function __construct()
    {
        $this->MODULE_ID = "itbizon.scratch";
        $this->MODULE_NAME = Loc::getMessage('ITB_TEST.MODULE_NAME');
        $this->MODULE_DESCRIPTION = "";
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "2021-05-11 12:00:00";
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID))
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.INSTALLED'));
        else
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.INSTALLERROR'));

        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID))
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.UNSTALLED'));
        else
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.UNSTALLERROR'));
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

}