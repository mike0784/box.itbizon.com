<?php

use Bitrix\Main\Application;
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
        $this->InstallDB();
        RegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("INSTALL"), $DOCUMENT_ROOT."/local/modules/itbizon.kalinin/install/step1.php");
    }

    /**
     * Uninstallation
     */
    public function DoUninstall()
    {
        global $APPLICATION, $DOCUMENT_ROOT;
        $this->UnInstallDB();
        UnRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(Loc::getMessage("UNINSTALL"), $DOCUMENT_ROOT."/local/modules/itbizon.kalinin/install/unstep1.php");
    }

    public function InstallDB()
    {
        try
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/ship.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/station.php');

            $db = Application::getConnection();
            $entities = [
                Itbizon\Kalinin\Lib\Model\StationTable::getEntity(),
                Itbizon\Kalinin\Lib\Model\ShipTable::getEntity(),
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

    public function UnInstallDB()
    {
        try
        {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/ship.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.kalinin/lib/model/station.php');

            $db = Application::getConnection();
            $entities = [
                Itbizon\Kalinin\Lib\Model\StationTable::getEntity(),
                Itbizon\Kalinin\Lib\Model\ShipTable::getEntity(),
            ];

            foreach ($entities as $entity)
            {
                if ($db->isTableExists($entity->getDBTableName()))
                    $db->dropTable($entity->getDBTableName());
            }
            return true;
        }
        catch (Exception $e)
        {
            return false;
        }
    }
}