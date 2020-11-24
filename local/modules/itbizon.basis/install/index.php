<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\ModuleManager;

class itbizon_basis extends CModule
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->MODULE_ID = "itbizon.basis";
        $this->MODULE_NAME = "[BizON] Работа с basis";
        $this->MODULE_DESCRIPTION = "";
        $this->MODULE_VERSION = "0.0.1";
        $this->MODULE_VERSION_DATE = "2020-11-20 00:00:00";
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    /**
     * Install
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallEvents();
            CAdminMessage::ShowNote('Тестовый модуль установлен');
        } else {
            CAdminMessage::ShowNote('Ошибка установки тестого модуля');
        }

        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * Uninstall
     */
    public function DoUninstall()
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallEvents();
            CAdminMessage::ShowNote('Тестовый модуль удален');
        } else {
            CAdminMessage::ShowNote('Ошибка удаления тестого модуля');
        }

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool
     */
    public function InstallDB()
    {
        return true;
    }

    /**
     *
     */
    public function UnInstallDB()
    {
        return true;
    }

    public function InstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->registerEventHandlerCompatible('main', 'OnUserTypeBuildList', 'itbizon.basis', \Itbizon\Basis\Types\WeekDayType::class, 'GetUserTypeDescription');
        $eventManager->registerEventHandlerCompatible('tasks', 'OnBeforeTaskAdd', 'itbizon.basis', \Itbizon\Basis\Utils\Handler::class, 'onBeforeTaskAdd');
        $eventManager->registerEventHandlerCompatible('tasks', 'OnBeforeTaskUpdate', 'itbizon.basis', \Itbizon\Basis\Utils\Handler::class, 'onBeforeTaskUpdate');
    }

    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();
        $eventManager->unRegisterEventHandler('main', 'OnUserTypeBuildList', 'itbizon.basis', \Itbizon\Basis\Types\WeekDayType::class, 'GetUserTypeDescription');
        $eventManager->unRegisterEventHandler('tasks', 'OnBeforeTaskAdd', 'itbizon.basis', \Itbizon\Basis\Utils\Handler::class, 'onBeforeTaskAdd');
        $eventManager->unRegisterEventHandler('tasks', 'OnBeforeTaskUpdate', 'itbizon.basis', \Itbizon\Basis\Utils\Handler::class, 'onBeforeTaskUpdate');
    }
}