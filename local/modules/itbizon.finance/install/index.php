<?php

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\UrlRewriter;

Loc::loadMessages(__FILE__);

class itbizon_finance extends CModule
{
    public $MODULE_ID = 'itbizon.finance';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $MODULE_GROUP_RIGHTS = "Y";

    /**
     * bizon_finance constructor.
     */
    public function __construct()
    {
        $arModuleVersion = [];
        include(__DIR__ . '/version.php');
        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }
        $this->MODULE_NAME = Loc::getMessage('ITB_FIN.MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('ITB_FIN.MODULE_DESCRIPTION');
        $this->PARTNER_NAME = Loc::getMessage('ITB_FIN.PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('ITB_FIN.PARTNER_URI');
    }

    /**
     * @return array
     */
    function GetModuleTasks()
    {
        return [
            'itbizon_finance_denied' => [
                'LETTER' => 'D',
                'BINDING' => 'module',
                'OPERATIONS' => [

                ],
            ],
            'itbizon_finance_standart' => [
                'LETTER' => 'N',
                'BINDING' => 'module',
                'OPERATIONS' => [

                ],
            ],
            'itbizon_finance_full' => [
                'LETTER' => 'X',
                'BINDING' => 'module',
                'OPERATIONS' => [

                ],
            ],
        ];
    }

    /**
     * Remove DB tables
     */
    public function UnInstallDB()
    {
        return true;
    }

    /**
     * Install module
     * @throws ArgumentNullException
     */
    public function DoInstall()
    {
        if (!ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();
            $this->InstallTasks();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_FIN.MODULE_INSTALL'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_FIN.MODULE_ALREADY_INSTALL'));
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }

    /**
     * @return bool|void
     * @throws ArgumentNullException
     */
    function InstallFiles()
    {
        $arRule = [
            'finance.income' => 'income',
            'finance.vault.router' => 'vault',
            'finance.category.router' => 'category',
            'finance.operation.router' => 'operation',
            'finance.stock.list' => 'stock',
            'finance.permission.list' => 'access',
            'finance.period.list' => 'planning'
        ];

        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/local/components/itbizon', true, true);
        CopyDirFiles(__DIR__ . '/activities', $_SERVER['DOCUMENT_ROOT'] . '/local/activities', true, true);
        CopyDirFiles(__DIR__ . '/finance', $_SERVER['DOCUMENT_ROOT'] . '/finance', true, true);

        foreach ($arRule as $id => $name) {
            UrlRewriter::add(SITE_ID, [
                "CONDITION" => "#^/finance/" . $name . "/#",
                "RULE" => "",
                "ID" => "itbizon:" . $id,
                "PATH" => "/finance/" . $name . "/index.php"
            ]);
        }
        return true;
    }

    /**
     * Create DB tables
     */
    public function InstallDB()
    {
        try {
            $files = [
                'accessright',
                'operationcategory',
                'vaulthistory',
                'vaultgroup',
                'vault',
                'stock',
                'operation',
                'operationaction',
                'period',
                'request',
                'requesttemplate',
                'categorybind'
            ];
            foreach ($files as $file) {
                require_once($_SERVER['DOCUMENT_ROOT'] . '/local/modules/itbizon.finance/lib/model/' . $file . '.php');
            }
            $db = Application::getConnection();
            $tables = [
                Itbizon\Finance\Model\AccessRightTable::getEntity(),
                Itbizon\Finance\Model\OperationCategoryTable::getEntity(),
                Itbizon\Finance\Model\VaultHistoryTable::getEntity(),
                Itbizon\Finance\Model\VaultGroupTable::getEntity(),
                Itbizon\Finance\Model\VaultTable::getEntity(),
                Itbizon\Finance\Model\StockTable::getEntity(),
                Itbizon\Finance\Model\OperationTable::getEntity(),
                Itbizon\Finance\Model\OperationActionTable::getEntity(),
                Itbizon\Finance\Model\PeriodTable::getEntity(),
                Itbizon\Finance\Model\RequestTable::getEntity(),
                Itbizon\Finance\Model\RequestTemplateTable::getEntity(),
                Itbizon\Finance\Model\CategoryBindTable::getEntity(),
            ];

            /** @var Bitrix\Main\ORM\Entity $entity */
            foreach ($tables as $entity) {
                if (!$db->isTableExists($entity->getDBTableName()))
                    $entity->createDbTable();
            }
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
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandlerCompatible('im', 'OnBeforeConfirmNotify', 'itbizon.finance', '\\Itbizon\\Finance\\Handler', 'onBeforeConfirmNotify');
        $eventManager->registerEventHandler('iblock', 'OnIBlockPropertyBuildList', 'itbizon.finance', '\\Itbizon\\Finance\\Properties\\Category', 'getUserTypeDescription');
        $eventManager->registerEventHandler('iblock', 'OnIBlockPropertyBuildList', 'itbizon.finance', '\\Itbizon\\Finance\\Properties\\Vault', 'getUserTypeDescription');
        $eventManager->registerEventHandler('main', 'OnUserTypeBuildList', 'itbizon.finance', '\\Itbizon\\Finance\\UserTypes\\Category', 'getUserTypeDescription');
        $eventManager->registerEventHandler('itbizon.finance', 'onAfterOperationCancel', 'itbizon.finance', '\\Itbizon\\Finance\\Handler', 'onAfterOperationCancel');
        $eventManager->registerEventHandler('itbizon.finance', 'onAfterOperationCommit', 'itbizon.finance', '\\Itbizon\\Finance\\Handler', 'onAfterOperationCommit');
    }

    /**
     * Uninstall module
     * @throws ArgumentNullException
     */
    public function DoUninstall() // todo Убрать удаление
    {
        if (ModuleManager::isModuleInstalled($this->MODULE_ID)) {
            //$this->UnInstallFiles();
            $this->UnInstallTasks();
            $this->UnInstallEvents();
            //$this->UnInstallDB();
            CAdminMessage::ShowNote(Loc::getMessage('ITB_FIN.MODULE_UNINSTALL'));
        } else {
            CAdminMessage::ShowNote(Loc::getMessage('ITB_FIN.MODULE_ALREADY_UNINSTALL'));
        }
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @return bool|void
     * @throws ArgumentNullException
     */
    function UnInstallFiles()
    {
        DeleteDirFilesEx('/finance');
        self::DeleteDirs(__DIR__ . '/activities', '/local/activities');
        self::DeleteDirs(__DIR__ . '/components', '/local/components/itbizon');

        // Если каталог пуст, то удаляем
        if (count(glob($_SERVER['DOCUMENT_ROOT'] . '/local/components/itbizon/*')) === 0) {
            rmdir($_SERVER['DOCUMENT_ROOT'] . '/local/components/itbizon/');
        }

        $arRule = [
            'finance.income',
            'finance.vault.router',
            'finance.category.router',
            'finance.operation.router',
            'finance.stock.list',
            'finance.permission.list',
            'finance.period.list',
        ];

        foreach ($arRule as $id) {
            UrlRewriter::delete(SITE_ID, [
                'ID' => "itbizon:" . $id
            ]);
        }
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
            if (!$d)
                throw new Exception("Error open dir");
            while ($entry = $d->read()) {
                if ($entry == "." || $entry == "..") continue;
                DeleteDirFilesEx($toDirs . '/' . $entry);
            }
            $d->close();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Unregister module event handlers
     */
    public function UnInstallEvents()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler('im', 'OnBeforeConfirmNotify', 'itbizon.finance', '\\Itbizon\\Finance\\Handler', 'onBeforeConfirmNotify');
        $eventManager->unRegisterEventHandler('iblock', 'OnIBlockPropertyBuildList', 'itbizon.finance', '\\Itbizon\\Finance\\Properties\\Category', 'getUserTypeDescription');
        $eventManager->unRegisterEventHandler('iblock', 'OnIBlockPropertyBuildList', 'itbizon.finance', '\\Itbizon\\Finance\\Properties\\Vault', 'getUserTypeDescription');
        $eventManager->unRegisterEventHandler('main', 'OnUserTypeBuildList', 'itbizon.finance', '\\Itbizon\\Finance\\UserTypes\\Category', 'getUserTypeDescription');
    }
}
