<?php

use Bitrix\Main\Loader;
use Itbizon\Service\Activities\Activity;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true || !Loader::includeModule('itbizon.service')) {
    die();
}

if(Activity::includeActivityClass(__DIR__)) {
    $arActivityDescription = [
        'NAME'        => '[itbizon] Управление уведомлениями',
        'DESCRIPTION' => 'Установка заданных настроек уведомлений',
        'TYPE'        => ['activity'],
        'CLASS'       => 'ItbizonSetNotify',
        'JSCLASS'     => 'BizProcActivity',
        'CATEGORY'    => [
            'ID' => 'other',
        ],
        'RETURN' => CBPItbizonSetNotify::getReturnDescription(),
    ];
}