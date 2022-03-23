<?php
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class itbizon_mike extends CModule
{
    public function  __construct()
    {
        $this -> MODULE_ID = 'itbizon.mike';
        require_once(__DIR__.'/version.php');
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
            /*if(!$this->InstallFiles())
			{
				CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_OK'));
				return false;
			}*/
            if($this->InstallDB())
			{
				CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_DB_OK'));
			}
			else{
				CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_DB_ERROR'));
				return false;
			}
            $this->InstallFiles();
            $this->InstallEvents();
            //$this->InstallAgents();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_OK'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_INSTALL_ERROR'));
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            //$this->UnInstallFiles();
            $this->UnInstallEvents();
            $this->UnInstallAgents();
			if($this->UnInstallDB()) CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_UNINSTALL_DB_OK'));
			else {
				CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_UNINSTALL_DB_ERROR'));
				return false;
			}
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_UNINSTALL_OK'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_MIKE_MODULE_UNINSTALL_ERROR'));
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        global $DB, $APPLICATION;
		$this->errors = false;
		// db
		$this->errors = $DB->runSQLBatch(
			$_SERVER['DOCUMENT_ROOT'] ."/local/modules/itbizon.mike/install/db/mysql/install.sql"
		);
		if ($this->errors !== false)
		{
			$APPLICATION->throwException(implode('', $this->errors));
			return false;
		}

		return true;
	}

    public function UnInstallDB()
    {
        global $APPLICATION, $DB;

		$errors = false;

		// delete DB
		$errors = $DB->runSQLBatch(
				$_SERVER['DOCUMENT_ROOT'] .'/local/modules/itbizon.mike/install/db/mysql/uninstall.sql'
			);
		
		if ($errors !== false)
		{
			$APPLICATION->throwException(implode('', $errors));
			return false;
		}

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
        if(!CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components', true, true))
        {
            $GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage('ITB_MIKE_MODULE_ERROR_COPY_FILES'));
            return false;
        }
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

    public function InstallAgents(): void
    {
        CAgent::AddAgent("\\Itbizon\\Mike\\Log::agent();",
            $this->MODULE_ID,
            "Y",
            86400,
            "",
            "Y",
            \Bitrix\Main\Type\DateTime::createFromPhp((new DateTime())->modify('tomorrow')),
            10);

        CAgent::AddAgent("\\Itbizon\\Mike\\Monitor::agent();",
            $this->MODULE_ID,
            "Y",
            3600,
            "",
            "Y",
            \Bitrix\Main\Type\DateTime::createFromPhp((new DateTime())->setTime(date('H') + 1, 0, 0)),
            20);
    }

    /**
     *
     */
    public function UnInstallAgents(): void
    {
        CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}