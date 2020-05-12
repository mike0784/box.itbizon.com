<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;

class itbizon_kulakov extends CModule
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->MODULE_ID = "itbizon.kulakov";
        $this->MODULE_NAME = "[BizON] Тестовый модуль";
        $this->MODULE_DESCRIPTION = "Модуль от Кулакова";
        $this->MODULE_VERSION = "0.1";
        $this->MODULE_VERSION_DATE = "2020-05-07 01:00:00";
    }

    /**
     * Install
     */
    public function DoInstall()
    {
        if(!ModuleManager::isModuleInstalled($this->MODULE_ID))
        {
            CAdminMessage::ShowNote('Тестовый модуль установлен');
            $this->InstallDB();
        } else
        {
            CAdminMessage::ShowNote('Ошибка установки тестого модуля');
        }

        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Uninstall
     */
    public function DoUninstall()
    {
        if(ModuleManager::isModuleInstalled($this->MODULE_ID))
        {
            CAdminMessage::ShowNote('Тестовый модуль удален');
            $this->UnInstallDB();
        }
        else
        {
            CAdminMessage::ShowNote('Ошибка удаления тестого модуля');
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool
     */
    public function InstallDB()
    {
        try
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kulakov/lib/orm/itbinvoice.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kulakov/lib/orm/itbproduct.php');

            $db = Application::getConnection();
            $entities = [
                Itbizon\Kulakov\Orm\ItbProductTable::getEntity(),
                Itbizon\Kulakov\Orm\ItbInvoiceTable::getEntity(),
            ];

            foreach ($entities as $entity)
            {
                if (!$db->isTableExists($entity->getDBTableName()))
                $entity->createDbTable();
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }

    /**
     *
     */
    public function UnInstallDB()
    {

    }
}