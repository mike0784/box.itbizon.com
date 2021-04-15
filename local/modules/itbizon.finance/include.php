<?php
define('ITB_FIN_STOCK_LOGIC_ON', true);

use \Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//Load require modules
$requireModules = ['crm', 'im'];
foreach ($requireModules as $moduleId) {
    if (!Loader::includeModule($moduleId)) {
        throw new Exception(
            str_replace(
                '#MODULE#',
                $moduleId,
                Loc::getMessage('ITB_FIN.ERROR_INCLUDE_MODULE')
            )
        );
    }
}

CJSCore::RegisterExt('itbizon.finance.bootstrap4', [
    'js' => [
        '/local/modules/itbizon.finance/extension/bootstrap4/js/bootstrap.min.js',
    ],
    'css' => [
        '/local/modules/itbizon.finance/extension/bootstrap4/css/bootstrap.min.css',
    ],
    'rel' => ["jquery2"],
    "skip_core" => true,
]);

CJSCore::RegisterExt('itbizon.finance.select2', [
    'js' => [
        '/local/modules/itbizon.finance/extension/select2/js/select2.min.js',
    ],
    'css' => [
        '/local/modules/itbizon.finance/extension/select2/css/select2.min.css',
    ],
    'rel'  => [],
    "skip_core" => true,
]);
