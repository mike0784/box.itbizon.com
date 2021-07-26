<?php


namespace Itbizon\Service;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;

/**
 * Class Utils
 * @package Itbizon\Service
 */
class Utils
{
    protected static $extensions;

    /**
     * Return required PHP extension list
     * @return string[]
     */
    public static function getRequiredExtensionList(): array
    {
        return ['curl', 'json', 'zip', 'Phar'];
    }

    /**
     * Get loaded and active PHP extensions list
     * @return array
     */
    public static function getExtensionList(): array
    {
        if(!self::$extensions) {
            self::$extensions = get_loaded_extensions();
        }
        return self::$extensions;
    }

    /**
     * Check extension activity by name
     * @param string $extension
     * @return bool
     */
    public static function isExtensionActive(string $extension): bool
    {
        return in_array($extension, self::getExtensionList());
    }

    /**
     * Return required Bitrix modules list
     * @return string[]
     */
    public static function getRequiredModuleList(): array
    {
        return ['main', 'mail', 'crm', 'tasks'];
    }

    /**
     * @param string $module
     * @return bool
     */
    public static function isModuleInstalled(string $module): bool
    {
        return ModuleManager::isModuleInstalled($module);
    }

    /**
     * @param array $messages
     * @return bool
     */
    public static function checkEngine(array &$messages): bool
    {
        $result = true;

        //Check PHP extensions
        foreach(self::getRequiredExtensionList() as $extension) {
            if(!self::isExtensionActive($extension)) {
                $messages[] = str_replace(['#EXTENSION#'], [$extension], Loc::getMessage('ITB_SERV_UTILS_EXTENSION_NOT_ACTIVE'));
                $result &= false;
            }
        }

        //Check bitrix modules
        foreach(self::getRequiredModuleList() as $module) {
            if(!self::isModuleInstalled($module)) {
                $messages[] = str_replace(['#MODULE#'], [$module], Loc::getMessage('ITB_SERV_UTILS_MODULE_NOT_INSTALL'));
                $result &= false;
            }
        }

        if($result) {
            $messages[] = Loc::getMessage('ITB_SERV_UTILS_CHECK_SUCCESS');
        }
        return $result;
    }
}