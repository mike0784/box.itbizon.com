<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME'        => 'Редактировать элементы истории полей',
    'DESCRIPTION' => 'Компонент для редактирования всякой штуки',
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