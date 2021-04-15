<?

use \Bitrix\Main\Loader;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!Loader::includeModule('itbizon.finance'))
    return;

$arComponentParameters = [
    'GROUPS' => [],
    'PARAMETERS' => [
        'CURRENT_USER' => '1',
        'FIELD_NAME' => 'USER_SELECT',
        'FIELD_ID' => 'USER_SELECT',
        'TITLE' => 'Выбранный пользователь',
        'CHANGE_ACTIVE' => true
    ],
];