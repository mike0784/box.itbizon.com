<?php

use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

Loc::loadMessages(__FILE__);

class itbizon_ttravel extends CModule
{
    public $MODULE_ID = 'itbizon.ttravel';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = 'Y';

    /**
     * itbizon_ttravel constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');
        if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_NAME        = '[BizON] Transformator Travel';
        $this->MODULE_DESCRIPTION = '';
        $this->PARTNER_NAME       = 'itbizon';
        $this->PARTNER_URI        = 'https://itbizon.com';
    }

    /**
     * @return array
     */
    function GetModuleTasks()
    {
        return [
            'itbizon_ttravel_denied' => [
                'LETTER' => 'D',
                'BINDING' => 'module',
                'OPERATIONS' => [

                ],
            ],
            'itbizon_ttravel_standart' => [
                'LETTER' => 'N',
                'BINDING' => 'module',
                'OPERATIONS' => [

                ],
            ],
            'itbizon_ttravel_full' => [
                'LETTER' => 'X',
                'BINDING' => 'module',
                'OPERATIONS' => [

                ],
            ],
        ];
    }

    /**
     * Create DB tables
     */
    public function InstallDB()
    {
        try {

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Remove DB tables
     */
    public function UnInstallDB()
    {
        try {

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Register module event handlers
     */
    public function InstallEvents()
    {
        //$eventManager = EventManager::getInstance();
    }

    /**
     * Unregister module event handlers
     */
    public function UnInstallEvents()
    {
        //$eventManager = EventManager::getInstance();
    }

    /**
     * Install module
     * @throws ArgumentNullException
     */
    public function DoInstall()
    {
        if(!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallTasks();
            CAdminMessage::ShowNote('Install complete');
        } else {
            CAdminMessage::ShowNote('Error insstall');
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @return bool|void
     * @throws ArgumentNullException
     */
    function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/activities', $_SERVER['DOCUMENT_ROOT'] . '/local/activities', true, true);
        return true;
    }

    /**
     * Uninstall module
     * @throws ArgumentNullException
     */
    public function DoUninstall()
    {
        if(ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->UnInstallFiles();
            $this->UnInstallTasks();
            $this->UnInstallEvents();
            //$this->UnInstallDB();
            CAdminMessage::ShowNote('Uninstalled');
        } else {
            CAdminMessage::ShowNote('Error unistall');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool|void
     * @throws ArgumentNullException
     */
    function UnInstallFiles()
    {
        return true;
    }

    /**
     * @param $frDirs
     * @param $toDirs
     * @return bool
     */
    static private function DeleteDirs($frDirs, $toDirs)
    {
        try {
            $d = dir($frDirs);
            while ($entry = $d->read()) {
                if($entry=="." || $entry=="..") continue;
                DeleteDirFilesEx($toDirs . '/' . $entry);
            }
            $d->close();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }
}
