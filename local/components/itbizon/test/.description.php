<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Тестовый компонент',
    'DESCRIPTION' => 'Тестовый компонент',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'itbizon',
        'NAME'  => 'Бизон',
        'CHILD' => [
            'ID'   => 'main',
            'NAME' => 'Основное',
        ],
    ],
];