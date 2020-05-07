<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule("itbizon.template")) {
    return;
}
$arComponentParameters = [
    'PARAMETERS' => [
        'SEF_MODE' => [
            'index'  => [
                'NAME'      => 'таблица',
                'DEFAULT'   => '',
                'VARIABLES' => [],
            ],
            'edit'   => [
                'NAME'      => 'Просмотр и редактирование',
                'DEFAULT'   => '#ID#/edit/',
                'VARIABLES' => ['ID'],
            ],
        ],
    ],
];

?>
