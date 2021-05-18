<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

use Itbizon\Scratch\Model\Box;

Loc::loadMessages(__FILE__);

/**
 * Class itbizon_scratch
 */
class itbizon_scratch extends CModule
{
    public $MODULE_ID = 'itbizon.scratch';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    /**
     * itbizon_scratch constructor.
     */
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
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallDB();
            $this->InstallEvents();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.INSTALLED'));
        } else
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.INSTALLERROR'));

        ModuleManager::registerModule($this->MODULE_ID);
    }

    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID))
            //$this->UnInstallDB(); // fixme for test
            $this->UnInstallEvents();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.UNSTALLED'));
        else
            CAdminMessage::ShowNote(Loc::getMessage('ITB_TEST.MODULE.UNSTALLERROR'));
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool
     */
    public function InstallDB()
    {
        //* new method
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/itbizon.scratch/install/db/mysql/install.sql");

        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
        // end new method */

        /* old method
        $files = [
            'box',
            'thing',
        ];
        foreach ($files as $file) {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.scratch/lib/model/' . $file . '.php');
        }

        $db = Application::getConnection();
        $tables = [
            Itbizon\Scratch\Model\BoxTable::getEntity(),
            Itbizon\Scratch\Model\ThingTable::getEntity(),
        ];

        // @var Bitrix\Main\ORM\Entity $entity
        foreach ($tables as $entity) {
            if (!$db->isTableExists($entity->getDBTableName()))
                $entity->createDbTable();
        }
        // end old method */

        return true;
    }

    public function UnInstallDB()
    {
        //*
        global $DB;
        $this->errors = false;
        $this->errors = $DB->RunSQLBatch($_SERVER['DOCUMENT_ROOT'] . "/local/modules/itbizon.scratch/install/db/mysql/uninstall.sql");
        if (!$this->errors) {
            return true;
        } else
            return $this->errors;
        // */
        //return true;
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();

    }


    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();


    }

}