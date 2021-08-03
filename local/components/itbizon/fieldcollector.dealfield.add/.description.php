<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Добавление полей в историю',
    'DESCRIPTION' => 'Компонент для добавления всякой штуки',
    'ICON'        => '/images/icon.gif',
    'PATH'        => [
        'ID'    => 'itbizon',
        'NAME'  => 'Бизон',
        'CHILD' => [
            'ID'   => 'fieldcollector.dealfield',
            'NAME' => 'Журнал полей',
        ],
    ],
];