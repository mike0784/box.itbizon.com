<?php

use Bitrix\Main\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Itbizon\Template\Utils\Transliteration;

Loader::includeModule('bizon.main');

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
    'OnBeforeUserUpdate',
    function(&$arFields) {
        
        $list = \Bitrix\Main\UserTable::getList([
            'filter'=>[
                '=ID'=>$arFields['ID'],
            ],
            'select'=>[
                'UF_DEPARTMENT',
            ],
        ])->fetch();
        
        $before = implode(';', $list['UF_DEPARTMENT']);
        $after = implode(';', $arFields['UF_DEPARTMENT']);
        
        // Если у сотрудника изменился отдел
        if($before !== $after)
            $arFields['DEPARTMENT_IS_CHANGE'] = true;
    }
);

$eventManager->addEventHandler(
    'main',
    'OnAfterUserUpdate',
    function(&$arFields) {
        $log = new \Bizon\Main\Log('userDepartmentTest');
        
        // Если у сотрудника изменился отдел
        if($arFields['DEPARTMENT_IS_CHANGE'])
        {
//            $log->Add('DEPARTMENT CHANGE');
//
//            if(Loader::includeModule('bizon.main'))
//            {
//                $result = \Bizon\Main\UserDepartment\Model\UserDepartmentTable::add(
//                    [
//                        'USER_ID'=>$arFields['ID'],
//                        'DEPARTMENT'=>$arFields['UF_DEPARTMENT'],
//                    ]
//                );
//                if($result->isSuccess())
//                    $log->Add('У сотрудника '.$arFields['ID'].' изменился отдел');
//                else
//                {
//                    $log->Add('Изменения отдела не сохранилось:');
//                    $log->Add($result->getErrorMessages());
//                    $log->Add('Новый отдел:');
//                    $log->Add($arFields['UF_DEPARTMENTS']);
//                }
//            }
//            else
//            {
//                $log->Add('Изменения отдела не сохранилось. Ошибка загрузки модуля bizon.main. Новый отдел:');
//                $log->Add($arFields['UF_DEPARTMENTS']);
//            }
        }
    }
);

?>