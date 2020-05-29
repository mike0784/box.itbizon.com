<?php

use Bitrix\Main\Application;
use Bitrix\Main\ModuleManager;
use Itbizon\Meleshev\Model\AutoTable;
use Itbizon\Meleshev\Model\ShopTable;

class itbizon_meleshev extends CModule
{
    public $MODULE_ID;
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__.'/version.php');
        if(is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion))
        {
            $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID          = 'itbizon.meleshev';
        $this->MODULE_NAME        = '[BizON] Тестовый модуль НМ';
        $this->MODULE_DESCRIPTION = 'Обычный тестовый модуль';
    }

    public function InstallDB()
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.meleshev/lib/model/auto.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.meleshev/lib/model/shop.php');
        try {
            $db = Application::getConnection();
            $entities = [
                AutoTable::getEntity(),
                ShopTable::getEntity()
            ];
            foreach ($entities as $entity) {
                if (!$db->isTableExists($entity->getDBTableName())) {
                    $entity->createDbTable();
                }
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }

    public function AddTestData()
    {
        $shops = [
            [
                "TITLE" => "Magazine",
                "AMOUNT" => 0,
                "COUNT" => 0,
                "COMMENT" => "Hello"
            ],
            [
                "TITLE" => "Shop",
                "AMOUNT" => 0,
                "COUNT" => 0,
                "COMMENT" => "Hi"
            ],
            [
                "TITLE" => "Auto",
                "AMOUNT" => 0,
                "COUNT" => 0,
                "COMMENT" => "Columbia Pictures"
            ],
            [
                "TITLE" => "Cars",
                "AMOUNT" => 0,
                "COUNT" => 0,
                "COMMENT" => "Best cars"
            ]
        ];
        $cars  = [
            [
                "SHOP_ID" => 1,
                "MARK" => "Super",
                "MODEL" => "Machine",
                "VALUE" => 100000,
                "IS_USED" => "Y",
                "COMMENT" => "Nice car"
            ],
            [
                "SHOP_ID" => 1,
                "MARK" => "Super Puper",
                "MODEL" => "Machine",
                "VALUE" => 200000,
                "IS_USED" => "Y"
            ],
            [
                "SHOP_ID" => 2,
                "MARK" => "Bad",
                "MODEL" => "Machine",
                "VALUE" => 500000,
                "IS_USED" => "Y",
                "COMMENT" => "Some problems"
            ],
            [
                "SHOP_ID" => 4,
                "MARK" => "Ford",
                "MODEL" => "Machine",
                "VALUE" => 1500000,
                "IS_USED" => "Y",
                "COMMENT" => ""
            ],
            [
                "SHOP_ID" => 4,
                "MARK" => "BMW",
                "MODEL" => "Machine",
                "VALUE" => 1500000,
                "IS_USED" => "Y",
                "COMMENT" => "Germany"
            ]
        ];

        foreach ($shops as $shop) {
            ShopTable::add($shop);
        }

        foreach ($cars as $auto) {
            AutoTable::add($auto);
        }
    }

    public function UnInstallDB()
    {
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.meleshev/lib/model/auto.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.meleshev/lib/model/shop.php');
        try {
            $db = Application::getConnection();
            $entities = [
                AutoTable::getEntity(),
                ShopTable::getEntity()
            ];
            /** @var \Bitrix\Main\ORM\Entity $entity */
            foreach ($entities as $entity) {
                if ($db->isTableExists($entity->getDBTableName())) {
                    $db->dropTable($entity->getDBTableName());
                }
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
            $this->AddTestData();
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