<?php

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
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
// User departments test
//-------------
$eventManager->addEventHandler(
    'main',
    'OnAfterUserRegister',
    function(&$arFields){
        if($arFields['UF_DEPARTMENT'])
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('OnAfterUserRegister');
                $log->Add('У нового сотрудника '.$arFields['ID'].' изменился отдел на:');
                $log->Add($arFields['UF_DEPARTMENT']);
            }
        }
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserSimpleRegister',
    function(&$arFields){
        if($arFields['UF_DEPARTMENT'])
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('OnAfterUserSimpleRegister');
                $log->Add('У нового сотрудника '.$arFields['ID'].' изменился отдел на:');
                $log->Add($arFields['UF_DEPARTMENT']);
            }
        }
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserAdd',
    function(&$arFields){
        if($arFields['UF_DEPARTMENT'])
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('OnAfterUserAdd');
                $log->Add('У нового сотрудника '.$arFields['ID'].' изменился отдел на:');
                $log->Add($arFields['UF_DEPARTMENT']);
            }
        }
    }
);

$eventManager->addEventHandler(
    'main',
    'OnBeforeUserUpdate',
    function(&$arFields) {

        $list = \Bitrix\Main\UserTable::getList([
            'filter'=>['=ID'=>$arFields['ID']],
            'select'=>['UF_DEPARTMENT', 'ACTIVE'],
        ])->fetch();

        $arFields['OLD_DEPARTMENT'] = $list['UF_DEPARTMENT'];
        $arFields['OLD_ACTIVE'] = $list['ACTIVE'];

        if(isset($arFields['UF_DEPARTMENT']))
        {
            $before = implode(';', $list['UF_DEPARTMENT']);
            $after = implode(';', $arFields['UF_DEPARTMENT']);

            // Если у сотрудника изменился отдел
            if($before !== $after)
                $arFields['DEPARTMENT_IS_CHANGE'] = true;
        }
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserUpdate',
    function(&$arFields) {
        // Если у сотрудника изменился отдел
        if($arFields['DEPARTMENT_IS_CHANGE'])
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('У сотрудника '.$arFields['ID'].' изменился отдел на:');
                $log->Add($arFields['UF_DEPARTMENT']);
                $log->Add('Старый отдел: ');
                $log->Add($arFields['OLD_DEPARTMENT']);
            }
            unset($arFields['DEPARTMENT_IS_CHANGE']);
        }
        // Если у сотрудника изменился ACTIVE
        if(isset($arFields['ACTIVE']) && $arFields['OLD_ACTIVE'] != $arFields['ACTIVE'])
        {
            $log = new \Bizon\Main\Log('userDepartmentTest');

            if($arFields['ACTIVE'] == 'Y')
            {
                $log->Add('Сотрудник '.$arFields['ID'].' добавлен в отдел: ');
                $log->Add($arFields['OLD_DEPARTMENT']);
            }
            else
            {
                $log->Add('Сотрудник '.$arFields['ID'].' уволен из отдела: ');
                $log->Add($arFields['OLD_DEPARTMENT']);
            }
        }
    }
);

// ----------------
// USER HEAD
// ----------------
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionAdd',
    function(&$arFields){
        // Изменение руководителей
        if($arFields['UF_HEAD'] > 0)
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('Добавлен руководитель для нового отдела: ' . $arFields['ID'] . ' - ' . $arFields['UF_HEAD']);
            }
        }
    }
);
$eventManager->addEventHandler(
    'iblock',
    'OnBeforeIBlockSectionUpdate',
    function(&$arFields){
        if(Loader::includeModule('intranet'))
        {
            $newHead = $arFields['UF_HEAD'];
            $oldHead = CIntranetUtils::GetStructure()['DATA'][$arFields['ID']]['UF_HEAD'];
    
            if($oldHead != $newHead)
                $arFields['OLD_HEAD'] = $oldHead;
        }
    }
);
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionUpdate',
    function(&$arFields){
        // Изменение руководителей
        if(isset($arFields['OLD_HEAD']))
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('Изменен руководитель отдела: ' . $arFields['ID'] . ' с ' . $arFields['OLD_HEAD'] . ' на ' . $arFields['UF_HEAD']);
            }
        }
    }
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionDelete',
    function(&$arFields){
        $head = CIntranetUtils::GetStructure()['DATA'][$arFields['ID']]['UF_HEAD'];
        // Изменение руководителей
        if($head)
        {
            if(Loader::includeModule('bizon.main'))
            {
                $log = new \Bizon\Main\Log('userDepartmentTest');
                $log->Add('Удален руководитель из отдела: '.$arFields['ID'].' - '.$head);
            }
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