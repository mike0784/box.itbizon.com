<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = [
    'PARAMETERS' => [
        'SEF_MODE' => [
            'index'  => [
                'NAME'      => 'Список станций',
                'DEFAULT'   => '/list/',
                'VARIABLES' => [],
            ],
            'edit'   => [
                'NAME'      => 'Редактирование',
                'DEFAULT'   => '/edit/#ID#/',
                'VARIABLES' => ['ID'],
            ],
        ],
    ],
];
