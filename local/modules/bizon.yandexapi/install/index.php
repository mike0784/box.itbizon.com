<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\Application;

class bizon_yandexapi extends CModule
{
    /**
     * itbizon_template constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_ID = 'bizon.yandexapi';
        $this->MODULE_NAME = '[BizON] Yandex API module';
        $this->MODULE_DESCRIPTION = 'Модуль для работы с методами Yandex API';
        $this->PARTNER_NAME = 'BizON';
        $this->PARTNER_URI = 'https://itbizon.com';
    }

    /**
     *
     */
    public function InstallDB()
    {
        try {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/bizon.yandexapi/lib/auth/model/oauth.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/bizon.yandexapi/lib/auth/model/pdd.php');

            $db = Application::getConnection();

            $entities = [
                \Bizon\Yandexapi\Auth\Model\OAuthTable::getEntity(),
                \Bizon\Yandexapi\Auth\Model\PDDTable::getEntity()
            ];

            /** @var \Bitrix\Main\ORM\Entity $entity */
            foreach ($entities as $entity) {
                if (!$db->isTableExists($entity->getDBTableName()))
                    $entity->createDbTable();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     */
    public function UnInstallDB()
    {
        try {
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/bizon.yandexapi/lib/auth/model/oauth.php');
            require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/bizon.yandexapi/lib/auth/model/pdd.php');

            $db = Application::getConnection();

            $entities = [
                \Bizon\Yandexapi\Auth\Model\OAuthTable::getEntity(),
                \Bizon\Yandexapi\Auth\Model\PDDTable::getEntity()
            ];

            /** @var \Bitrix\Main\ORM\Entity $entity */
            foreach ($entities as $entity) {
                if (!$db->isTableExists($entity->getDBTableName()))
                    $db->dropTable($entity->getDBTableName());
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     *
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallDB();
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
            $this->UnInstallDB();
            CAdminMessage::ShowNote('Модуль удален');
        } else {
            CAdminMessage::ShowNote('Ошибка удаления модуля');
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}