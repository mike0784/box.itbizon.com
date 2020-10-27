<?php

use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\EventManager;
use Itbizon\Departmenthistory\DepartmentHistory\Model\DepartmentHistoryTable;
use Itbizon\Departmenthistory\StructureHistory\Model\StructureHistoryTable;
use \Itbizon\Tourism\TravelPoint\Model\RegionTable;
use \Itbizon\Tourism\TravelPoint\Model\CountryTable;
use \Itbizon\Tourism\TravelPoint\Model\CityTable;

class itbizon_departmenthistory extends \CModule
{
    /**
     * itbizon_departmenthistory constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'itbizon.departmenthistory';
        $this->MODULE_NAME = '[BizON] История отделов';
        $this->MODULE_DESCRIPTION = 'Модуль для хранения истории отделов';
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
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
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.departmenthistory/lib/departmenthistory/model/departmenthistory.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.departmenthistory/lib/departmenthistory/model/structurehistory.php');
        $db = Application::getConnection();
        $entities = [
            DepartmentHistoryTable::getEntity(),
            StructureHistoryTable::getEntity(),
        ];
        /** @var \Bitrix\Main\ORM\Entity $entity */
        foreach ($entities as $entity) {
            if (!$db->isTableExists($entity->getDBTableName()))
                $entity->createDbTable();
        }
        return true;
    }

    /**
     *
     */
    public function UnInstallDB()
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.departmenthistory/lib/departmenthistory/model/departmenthistory.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.departmenthistory/lib/departmenthistory/model/structurehistory.php');
        $db = Application::getConnection();
        $entities = [
            DepartmentHistoryTable::getEntity(),
            StructureHistoryTable::getEntity(),
        ];
        /** @var \Bitrix\Main\ORM\Entity $entity */
        foreach ($entities as $entity) {
            if ($db->isTableExists($entity->getDBTableName()))
                $db->dropTable($entity->getDBTableName());
        }
        return true;
    }

    /**
     *
     */
    public function InstallEvents()
    {
        return true;
    }

    /**
     *
     */
    public function UnInstallEvents()
    {
        return true;
    }

    /**
     *
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallDB();
            $this->InstallFiles();
            $this->InstallEvents();
            $this->InstallTasks();
            CAdminMessage::ShowNote('Модуль установлен');
        } else {
            CAdminMessage::ShowNote('Ошибка установки модуля');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     *
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallTasks();
            $this->UnInstallEvents();
            $this->UnInstallFiles();
            $this->UnInstallDB();
            CAdminMessage::ShowNote('Модуль удален');
        } else {
            CAdminMessage::ShowNote('Ошибка удаления модуля');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}