<?php

use Bitrix\Main\Loader;
use Itbizon\Service\Activities\Activity;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true || !Loader::includeModule('itbizon.service'))
    die();

if(Activity::includeActivityClass(__DIR__)) {
    $arActivityDescription = [
        'NAME'        => 'Тестовое действие',
        'DESCRIPTION' => '',
        'TYPE'        => 'activity',
        'CLASS'       => 'ItbizonTest',
        'JSCLASS'     => 'BizProcActivity',
        'CATEGORY'    => [
            'OWN_ID' => 'itbizon.service',
            'OWN_NAME' => '[BizON] Сервис',
        ],
        'RETURN' => CBPItbizonTest::getReturnDescription(),
    ];
} 