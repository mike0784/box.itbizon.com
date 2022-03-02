<?php
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

/**
 * Class itbizon_service
 */
class itbizon_service extends CModule
{
    /**
     * itbizon_service constructor.
     */
    public function __construct()
    {
        $this->MODULE_ID = 'itbizon.service';
        $this->MODULE_NAME = Loc::getMessage('ITB_SERV_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ITB_SERV_MODULE_DESCRIPTION');
        include(__DIR__.'/version.php');
        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion))
        {
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
            if(!$this->InstallDB())
                return false;
            $this->InstallEvents();
            if(!$this->InstallFiles())
                return false;
            $this->InstallAgents();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_SERV_MODULE_INSTALL_OK'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_SERV_MODULE_INSTALL_ERROR'));
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Uninstall
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallAgents();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_SERV_MODULE_UNINSTALL_OK'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_SERV_MODULE_UNINSTALL_ERROR'));
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * Create DB tables
     * @return bool
     */
    public function InstallDB()
    {
        $db = Application::getConnection();
        $sqlFilePath = __DIR__.'/db/'.mb_strtolower($db->getType()).'/install.sql';
        file_put_contents(__DIR__."/log.php", var_export($sqlFilePath, true) );
        if(!file_exists($sqlFilePath)) {
            $GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage('ITB_SERV_MODULE_ERROR_NO_FILE')." ".$sqlFilePath);
            return false;
        }
        $errors = $db->executeSqlBatch(file_get_contents($sqlFilePath));
        if(!empty($errors)) {
            $GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage('ITB_SERV_MODULE_ERROR_SQL')." ".var_export($errors, true));
            return false;
        }
        return true;
    }

    /**
     * Remove DB tables
     */
    public function UnInstallDB()
    {
        return true;
    }

    /**
     * Install event handlers
     */
    public function InstallEvents()
    {
        return true;
    }

    /**
     * Uninstall event handlers
     */
    public function UnInstallEvents()
    {
        return true;
    }

    /**
     * @return bool|void
     */
    public function InstallFiles()
    {
        if(!CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/itbizon', true, true))
        {
            $GLOBALS["APPLICATION"]->ThrowException(Loc::getMessage('ITB_SERV_MODULE_ERROR_COPY_FILES'));
            return false;
        }
        return true;
    }

    /**
     * @return bool|void
     */
    public function UnInstallFiles()
    {
        return true;
    }

    /**
     * @throws Exception
     */
    public function InstallAgents(): void
    {
        CAgent::AddAgent("\\Itbizon\\Service\\Log::agent();",
            $this->MODULE_ID,
            "Y",
            86400,
            "",
            "Y",
            \Bitrix\Main\Type\DateTime::createFromPhp((new DateTime())->modify('tomorrow')),
            10);

        CAgent::AddAgent("\\Itbizon\\Service\\Monitor::agent();",
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