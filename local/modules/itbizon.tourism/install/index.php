<?php
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

class itbizon_tourism extends CModule
{
    /**
     * itbizon_template constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__.'/version.php');
        if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID          = 'itbizon.tourism';
        $this->MODULE_NAME        = '[BizON] Туризм';
        $this->MODULE_DESCRIPTION = 'Модуль для туристической компании';
        $this->PARTNER_NAME       = 'BizON';
        $this->PARTNER_URI        = 'https://itbizon.com';
    }

    /**
     *
     */
    public function InstallFiles()
    {
        return true;
    }

    /**
     *
     */
    public function UnInstallFiles()
    {

    }

    /**
     *
     */
    public function InstallDB()
    {
        try
        {
            return true;
        }
        catch(Exception $e)
        {

        }
        return false;
    }

    /**
     *
     */
    public function UnInstallDB()
    {

    }

    /**
     *
     */
    public function InstallEvents()
    {
        EventManager::getInstance()->RegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\\Itbizon\\Tourism\\Fields\\TravelPoint',
            'getUserTypeDescription'
        );
        EventManager::getInstance()->RegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\\Itbizon\\Tourism\\Fields\\ClassRate',
            'getUserTypeDescription'
        );
    }

    /**
     *
     */
    public function UnInstallEvents()
    {
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\\Itbizon\\Tourism\\Fields\\TravelPoint',
            'getUserTypeDescription'
        );
        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            '\\Itbizon\\Tourism\\Fields\\ClassRate',
            'getUserTypeDescription'
        );
    }

    /**
     *
     */
    public function DoInstall()
    {
        if(!ModuleManager::isModuleInstalled($this->MODULE_ID))
        {
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallEvents();
            $this->InstallTasks();
            CAdminMessage::ShowNote('Модуль установлен');
        }
        else
        {
            CAdminMessage::ShowNote('Ошибка установки модуля');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     *
     */
    public function DoUninstall()
    {
        if(ModuleManager::isModuleInstalled($this->MODULE_ID))
        {
            $this->UnInstallTasks();
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallDB();
            CAdminMessage::ShowNote('Модуль удален');
        }
        else
        {
            CAdminMessage::ShowNote('Ошибка удаления модуля');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}