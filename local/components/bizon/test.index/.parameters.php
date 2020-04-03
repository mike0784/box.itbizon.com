<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("itbizon.template")) {
    return;
}
$arComponentParameters = [
    'PARAMETERS' => [
        'SEF_MODE' => [
            'index'  => [
                'NAME'      => 'Шаблон таблицы',
                'DEFAULT'   => 'index.php',
                'VARIABLES' => [],
            ],
            'create'   => [
                'NAME'      => 'Шаблон настроек',
                'DEFAULT'   => 'create/',
                'VARIABLES' => [],
            ],
        ],
    ],
];

?>
