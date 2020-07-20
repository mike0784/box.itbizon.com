<?php

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bizon\Main\Log;
use Itbizon\Template\Utils\Transliteration;

//get instance EventManager
$eventManager = EventManager::getInstance();

//Add new event
$eventManager->addEventHandler(
    "itbizon.template",
    'OnAfterAddFine',
    ["Itbizon\Template\SystemFines\EventHandlers\FineHandler", "OnAfterAddFineHandler"]
);
//Add new event
$eventManager->addEventHandler(
    "itbizon.template",
    'OnAfterDeleteFine',
    "Itbizon\Template\SystemFines\EventHandlers\FineHandler::OnAfterDeleteFineHandler"
);

$eventManager->addEventHandler('crm', 'OnAfterCrmContactAdd',
    function (&$arrFields) {
        Loader::includeModule('itbizon.template');
        Transliteration::updateContactTransliterate($arrFields);
    });

$eventManager->addEventHandler('crm', 'OnAfterCrmContactUpdate',
    function (&$arrFields) {
        Loader::includeModule('itbizon.template');
        Transliteration::updateContactTransliterate($arrFields);
    });

//$eventManager->addEventHandler('crm', 'OnAfterCrmContactUpdate',
//   ['Itbizon\Template\SystemFines\EventHandlers\FineHandler', 'onAfterCrmContactUpdate']);


//-------------
// User departments change
//-------------
$eventManager->addEventHandler(
    'main',
    'OnAfterUserRegister',
    function(&$arFields){
        if($arFields['UF_DEPARTMENT'] && Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::addUser($arFields['ID'], $arFields['UF_DEPARTMENT'], 'Register');
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserSimpleRegister',
    function(&$arFields){
        if($arFields['UF_DEPARTMENT'] && Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::addUser($arFields['ID'], $arFields['UF_DEPARTMENT'], 'SimpleRegister');
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserAdd',
    function(&$arFields){
        if($arFields['UF_DEPARTMENT'] && Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::addUser($arFields['ID'], $arFields['UF_DEPARTMENT'], 'Add');
    }
);

$eventManager->addEventHandler(
    'main',
    'OnBeforeUserUpdate',
    function(&$arFields) {
        if(Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::beforeUserUpdate($arFields);
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserUpdate',
    function(&$arFields) {
        if(Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::afterUserUpdate($arFields);
    }
);

// ----------------
// user head change
// ----------------
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionAdd',
    function(&$arFields){
        // Изменение руководителей
        if($arFields['UF_HEAD'] > 0 && Loader::includeModule('bizon.main'))
        {
            $log = new \Bizon\Main\Log('department_change');
            $log->Add('Добавлен руководитель для нового отдела #' . $arFields['ID'] . ' - ' . $arFields['UF_HEAD']);
            \Bizon\Main\Department\Helper::saveHistoryDepartment($arFields['ID']);
        }
    }
);
$eventManager->addEventHandler(
    'iblock',
    'OnBeforeIBlockSectionUpdate',
    function(&$arFields){
        if(Loader::includeModule('bizon.main'))
        {
            $newHead = $arFields['UF_HEAD'];
            $oldHead = \Bizon\Main\Department\Helper::getStructureDepartments()[$arFields['ID']]['UF_HEAD'];
    
            if($oldHead != $newHead)
                $arFields['OLD_HEAD'] = intval($oldHead);
        }
    }
);
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionUpdate',
    function(&$arFields){
        // Изменение руководителей
        if(isset($arFields['OLD_HEAD']) && Loader::includeModule('bizon.main'))
        {
            $log = new \Bizon\Main\Log('department_change');
            $log->Add('Изменен руководитель отдела #' . $arFields['ID'] . ' с ' . $arFields['OLD_HEAD'] . ' на ' . $arFields['UF_HEAD']);
            \Bizon\Main\Department\Helper::saveHistoryDepartment($arFields['ID']);
        }
    }
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionDelete',
    function(&$arFields){
        $head = CIntranetUtils::GetStructure()['DATA'][$arFields['ID']]['UF_HEAD'];
        // Изменение руководителей
        if($head && Loader::includeModule('bizon.main'))
        {
            $log = new \Bizon\Main\Log('department_change');
            $log->Add('Удален руководитель из отдела #'.$arFields['ID'].' - '.$head);
            \Bizon\Main\Department\Helper::saveHistoryDepartment($arFields['ID']);
        }
    }
);

// ----------------------------------------
// Tasks check list test
// ----------------------------------------
$eventManager->addEventHandler(
    'tasks',
    '\Bitrix\Tasks\Internals\Task\CheckList::onAfterAdd',
    function(&$arFields)
    {
        if(Loader::includeModule('bizon.main'))
            \Bizon\Main\Tasks\CheckItem::onAfterCheckListAdd($arFields);
    }
);

$eventManager->addEventHandler(
    'tasks',
    '\Bitrix\Tasks\Internals\Task\CheckList::onAfterUpdate',
    function(&$arFields)
    {
        if(Loader::includeModule('bizon.main'))
            \Bizon\Main\Tasks\CheckItem::onAfterCheckListUpdate($arFields);
    }
);

//Перед добавление задачи
$eventManager->addEventHandler(
    'tasks',
    'OnTaskAdd',
    function (&$taskId) {
        if (Loader::includeModule('bizon.main') && Loader::includeModule('tasks')) {
            $userId = CUser::GetId();
            \Bizon\Main\Utils\AssistantAdministrator::changeTaskManager($taskId, $userId);
        }
    }
);
?>