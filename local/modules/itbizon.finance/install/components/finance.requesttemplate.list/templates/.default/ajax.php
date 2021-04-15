<?php

define("NO_KEEP_STATISTIC", "Y");
define("NO_AGENT_STATISTIC","Y");
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

header('Content-Type: application/json');

use Itbizon\Finance\Model\RequestTemplateTable;
use Itbizon\Finance\Model\RequestTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Itbizon\Finance\Permission;

Loc::loadMessages(__FILE__);

function answer($state, $message, $data = null)
{
    echo json_encode(['status'=>$state, 'message'=>$message, 'data'=>$data]);
    die();
}

try
{
    if(!Loader::includeModule('itbizon.finance'))
        throw new Exception(Loc::getMessage("ITB_FIN.REQ_TEMPLATE.ERROR.MODULE.FINANCE"));
    
    $request = $_REQUEST;
    $cmd = strval($request['cmd']);
    $userId = $request['userId'];
    $requestId = $request['requestId'];
    
    if ($cmd == 'decline')
    {
        $req = RequestTable::getById($requestId)->fetchObject();
        $result = $req->cancel($userId);

        if($result->isSuccess())
            answer(true, 'Success', $result);
        answer(false, implode(', ', $result->getErrorMessages()), $result);
    }
    elseif($_SERVER['REQUEST_METHOD'] === "GET" && !empty($_REQUEST['remove'])) {
        // Get id
        $id = intval($_REQUEST['remove']);
        $requestTemplate = RequestTemplateTable::getById($id)->fetchObject();
        
        if(!Permission::getInstance()->isAllowRequestTemplateDelete($requestTemplate))
            throw new Exception(Loc::getMessage("ITB_FIN.REQ_TEMPLATE.ERROR.ACCESS"));
    
        // Get object
        $result = $requestTemplate->delete();
        if(!$result->isSuccess())
            throw new Exception(Loc::getMessage("ITB_FIN.REQ_TEMPLATE.ERROR.DELETE").$result->getErrorMessages());
    
        answer(true, null);
    }
    else
        throw new Exception('ajax error');
}
catch(Exception $e)
{
    answer(false, $e->getMessage());
}