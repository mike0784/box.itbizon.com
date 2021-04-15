<?php


namespace Itbizon\Finance\UserTypes;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserField\Types\BaseType;
use CUserTypeManager;

/**
 * Class Category
 * @package Itbizon\Finance\UserTypes
 */
class Category extends BaseType
{
    public const
        USER_TYPE_ID = 'itb_finance_category',
        RENDER_COMPONENT = 'itbizon:finance.field.category';

    /**
     * @return array
     */
    public static function getDescription(): array
    {
        return [
            'DESCRIPTION' => Loc::getMessage('USER_TYPE_BOOL_DESCRIPTION'),
            'BASE_TYPE' => CUserTypeManager::BASE_TYPE_INT,
        ];
    }

    /**
     * @return string
     */
    public static function getDbColumnType(): string
    {
        return 'int(18)';
    }

    /**
     * @param array $userField
     * @return array
     */
    public static function prepareSettings(array $userField): array
    {
        $default = (int)$userField['SETTINGS']['DEFAULT_VALUE'];
        return [
            'DEFAULT_VALUE' => $default,
        ];
    }
}