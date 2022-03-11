<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Service\Processor;

define('NO_KEEP_STATISTIC', 'Y');
define('NO_AGENT_STATISTIC', 'Y');
define('NOT_CHECK_PERMISSIONS', true);
define('DisableEventsCheck', true);
define('NO_AGENT_CHECK', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

Loc::loadMessages(__FILE__);
try {
    if (!Loader::includeModule('itbizon.service')) {
        throw new Exception(Loc::getMessage('ITB_SERVICE.MODULE.TOOLS.AJAX.ERROR.LOAD.MODULE', ['#MODULE_NAME#' => 'itbizon.service']));
    }

    global $USER;
    if(!is_a($USER, CUser::class) || !$USER->IsAuthorized())
        throw new Exception(Loc::getMessage('ITB_SERVICE.MODULE.TOOLS.AJAX.ERROR.ACCESS_DENY'));

    $modules = isset($_REQUEST['modules']) ? $_REQUEST['modules'] : [];
    if (is_array($modules) && $modules) {
        foreach ($modules as $module) {
            if (!Loader::includeModule($module)) {
                throw new Exception(Loc::getMessage('ITB_SERVICE.MODULE.TOOLS.AJAX.ERROR.LOAD.MODULE', ['#MODULE_NAME#' => $module]));
            }
        }
    }
    $processor = new Processor();
    $process = isset($_REQUEST['process']) ? $_REQUEST['process'] : '';
    if (!is_callable($process)) {
        throw new Exception(Loc::getMessage('ITB_SERVICE.MODULE.TOOLS.AJAX.ERROR.PROCESS.NOTFOUND'));
    }
    $processor->run($process);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['state' => false, 'message' => $e->getMessage(), 'data' => []]);
    die;
}