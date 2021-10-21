<?php

use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Itbizon\Service\Log;


//get instance EventManager
$eventManager = EventManager::getInstance();

//Test product row lock
$eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealProductRowsSave',
    function ($id, $rows) {
        static $lock = false;
        if ($lock) {
            return;
        }
        $lock = true;
        if (Loader::includeModule('itbizon.service')) {
            $log = new Log('lock_row');
            try {
                if (!Loader::includeModule('im')) {
                    throw new Exception('Ошибка подключения модуля im');
                }

                $log->add($id);
                $log->add($rows);

                //Check
                $notBind = [];
                $changeName = [];
                foreach ($rows as $index => $row) {
                    $productId = intval($row['PRODUCT_ID']);
                    if ($productId === 0) {
                        $notBind[] = $index;
                    } else {
                        $product = \CCrmProduct::GetByID($productId);
                        if ($product) {
                            if ($product['NAME'] !== $row['PRODUCT_NAME']) {
                                $changeName[] = $index;
                                $rows[$index]['ORIGINAL_NAME'] = $product['NAME'];
                            }
                        }
                    }
                }

                //Messages
                $messages = [];
                foreach ($notBind as $index) {
                    $messages[] = 'Товарная позиция #' . $index . ' "' . $rows[$index]['PRODUCT_NAME'] . '" создана вручную';
                }
                foreach ($changeName as $index) {
                    $messages[] = 'В товарной позиции #' . $index . ' "' . $rows[$index]['PRODUCT_NAME'] . '" изменено название (оригинальное название "' .$rows[$index]['ORIGINAL_NAME']. '")';
                }
                if (!empty($messages)) {
                    global $USER;
                    array_unshift($messages, '[b]ВНИМАНИЕ[/b] по сделке [url=/crm/deal/details/' . $id . '/]#' . $id . '[/url] некорректно заполнены товарные позиции:');
                    //Current user (who change)
                    if ($USER && is_a($USER, \CUser::class)) {
                        \CIMNotify::Add([
                            'FROM_USER_ID' => 0,
                            'TO_USER_ID' => $USER->GetID(),
                            'NOTIFY_TYPE' => IM_NOTIFY_SYSTEM,
                            'NOTIFY_MESSAGE' => implode(PHP_EOL, $messages),
                            'NOTIFY_MODULE' => 'itbizon.service', //TODO
                            'NOTIFY_EVENT' => 'dealproductrow|'.$id
                        ]);
                    }

                    //Chief
                    //TODO

                    //Admins
                    //TODO
                }

                //Change product rows
                if (count($notBind) || count($changeName)) {
                    $newRows = [];
                    foreach ($rows as $index => $row) {
                        if (in_array($index, $notBind)) {
                            continue;
                        }
                        if (in_array($index, $changeName)) {
                            $row['PRODUCT_NAME'] = $row['ORIGINAL_NAME'];
                        }
                        $newRows[] = [
                            'PRODUCT_NAME' => $row['PRODUCT_NAME'],
                            'PRODUCT_ID' => $row['PRODUCT_ID'],
                            'QUANTITY' => $row['QUANTITY'],
                            'MEASURE_CODE' => $row['MEASURE_CODE'],
                            'MEASURE_NAME' => $row['MEASURE_NAME'],
                            'PRICE' => $row['PRICE'],
                            'PRICE_EXCLUSIVE' => $row['PRICE_EXCLUSIVE'],
                            'PRICE_NETTO' => $row['PRICE_NETTO'],
                            'PRICE_BRUTTO' => $row['PRICE_BRUTTO'],
                            'DISCOUNT_TYPE_ID' => $row['DISCOUNT_TYPE_ID'],
                            'DISCOUNT_RATE' => $row['DISCOUNT_RATE'],
                            'DISCOUNT_SUM' => $row['DISCOUNT_SUM'],
                            'TAX_RATE' => $row['TAX_RATE'],
                            'TAX_INCLUDED' => $row['TAX_INCLUDED'],
                        ];
                    }
                    $result = \CCrmProductRow::SaveRows('D', $id, $newRows);
                    if (!$result) {
                        throw new Exception('Ошибка изменения товарных позиций по сделке #' . $id . ': ' . \CCrmProductRow::GetLastError());
                    }
                }

            } catch (Exception $e) {
                $log->add($e->getMessage(), Log::LEVEL_ERROR);
            }
        }
    }
);


//Test product row lock
$eventManager->addEventHandler(
    'crm',
    'OnAfterCrmDealProductRowsSave',
    function ($id, $rows) {
        static $lock = false;
        if ($lock) {
            return;
        }
        $lock = true;
        if (Loader::includeModule('itbizon.service')) {
            $log = new Log('lock_row');
            try {
                if (!Loader::includeModule('im')) {
                    throw new Exception('Ошибка подключения модуля im');
                }

                $log->add($id);
                $log->add($rows);

                //Check
                $notBind = [];
                $changeName = [];
                foreach ($rows as $index => $row) {
                    $productId = intval($row['PRODUCT_ID']);
                    if ($productId === 0) {
                        $notBind[] = $index;
                    } else {
                        $product = \CCrmProduct::GetByID($productId);
                        if ($product) {
                            if ($product['NAME'] !== $row['PRODUCT_NAME']) {
                                $changeName[] = $index;
                                $rows[$index]['ORIGINAL_NAME'] = $product['NAME'];
                            }
                        }
                    }
                }

                //Messages
                $messages = [];
                foreach ($notBind as $index) {
                    $messages[] = 'Товарная позиция #' . $index . ' "' . $rows[$index]['PRODUCT_NAME'] . '" создана вручную';
                }
                foreach ($changeName as $index) {
                    $messages[] = 'В товарной позиции #' . $index . ' "' . $rows[$index]['PRODUCT_NAME'] . '" изменено название (оригинальное название "' .$rows[$index]['ORIGINAL_NAME']. '")';
                }
                if (!empty($messages)) {
                    global $USER;
                    array_unshift($messages, '[b]ВНИМАНИЕ[/b] по сделке [url=/crm/deal/details/' . $id . '/]#' . $id . '[/url] некорректно заполнены товарные позиции:');
                    //Current user (who change)
                    if ($USER && is_a($USER, \CUser::class)) {
                        \CIMNotify::Add([
                            'FROM_USER_ID' => 0,
                            'TO_USER_ID' => $USER->GetID(),
                            'NOTIFY_TYPE' => IM_NOTIFY_SYSTEM,
                            'NOTIFY_MESSAGE' => implode(PHP_EOL, $messages),
                            'NOTIFY_MODULE' => 'itbizon.service', //TODO
                            'NOTIFY_EVENT' => 'dealproductrow|'.$id
                        ]);
                    }

                    //Chief
                    //TODO

                    //Admins
                    //TODO
                }

                //Change product rows
                if (count($notBind) || count($changeName)) {
                    $newRows = [];
                    foreach ($rows as $index => $row) {
                        if (in_array($index, $notBind)) {
                            continue;
                        }
                        if (in_array($index, $changeName)) {
                            $row['PRODUCT_NAME'] = $row['ORIGINAL_NAME'];
                        }
                        $newRows[] = [
                            'PRODUCT_NAME' => $row['PRODUCT_NAME'],
                            'PRODUCT_ID' => $row['PRODUCT_ID'],
                            'QUANTITY' => $row['QUANTITY'],
                            'MEASURE_CODE' => $row['MEASURE_CODE'],
                            'MEASURE_NAME' => $row['MEASURE_NAME'],
                            'PRICE' => $row['PRICE'],
                            'PRICE_EXCLUSIVE' => $row['PRICE_EXCLUSIVE'],
                            'PRICE_NETTO' => $row['PRICE_NETTO'],
                            'PRICE_BRUTTO' => $row['PRICE_BRUTTO'],
                            'DISCOUNT_TYPE_ID' => $row['DISCOUNT_TYPE_ID'],
                            'DISCOUNT_RATE' => $row['DISCOUNT_RATE'],
                            'DISCOUNT_SUM' => $row['DISCOUNT_SUM'],
                            'TAX_RATE' => $row['TAX_RATE'],
                            'TAX_INCLUDED' => $row['TAX_INCLUDED'],
                        ];
                    }
                    $result = \CCrmProductRow::SaveRows('D', $id, $newRows);
                    if (!$result) {
                        throw new Exception('Ошибка изменения товарных позиций по сделке #' . $id . ': ' . \CCrmProductRow::GetLastError());
                    }
                }

            } catch (Exception $e) {
                $log->add($e->getMessage(), Log::LEVEL_ERROR);
            }
        }
    }
);

if(Loader::includeModule('bizon.main')) {

// Тест истории полей сделки
    //*
    $eventManager->addEventHandler(
        'crm',
        'OnAfterCrmDealUpdate',
        ['\Bizon\Main\Utils\DealHandler', 'OnAfterCrmDealUpdate']
    );
    // */
}


/*
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
    function (&$arFields) {
        if ($arFields['UF_DEPARTMENT'] && Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::addUser($arFields['ID'], $arFields['UF_DEPARTMENT'], 'Register');
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserSimpleRegister',
    function (&$arFields) {
        if ($arFields['UF_DEPARTMENT'] && Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::addUser($arFields['ID'], $arFields['UF_DEPARTMENT'], 'SimpleRegister');
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserAdd',
    function (&$arFields) {
        if ($arFields['UF_DEPARTMENT'] && Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::addUser($arFields['ID'], $arFields['UF_DEPARTMENT'], 'Add');
    }
);

$eventManager->addEventHandler(
    'main',
    'OnBeforeUserUpdate',
    function (&$arFields) {
        if (Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::beforeUserUpdate($arFields);
    }
);
$eventManager->addEventHandler(
    'main',
    'OnAfterUserUpdate',
    function (&$arFields) {
        if (Loader::includeModule('bizon.main'))
            \Bizon\Main\Department\Helper::afterUserUpdate($arFields);
    }
);

// ----------------
// user head change
// ----------------
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionAdd',
    function (&$arFields) {
        // Изменение руководителей
        if ($arFields['UF_HEAD'] > 0 && Loader::includeModule('bizon.main')) {
            $log = new \Bizon\Main\Log('department_change');
            $log->Add('Добавлен руководитель для нового отдела #' . $arFields['ID'] . ' - ' . $arFields['UF_HEAD']);
            \Bizon\Main\Department\Helper::saveHistoryDepartment($arFields['ID']);
        }
    }
);
$eventManager->addEventHandler(
    'iblock',
    'OnBeforeIBlockSectionUpdate',
    function (&$arFields) {
        if (Loader::includeModule('bizon.main')) {
            $newHead = $arFields['UF_HEAD'];
            $oldHead = \Bizon\Main\Department\Helper::getStructureDepartments()[$arFields['ID']]['UF_HEAD'];

            if ($oldHead != $newHead)
                $arFields['OLD_HEAD'] = intval($oldHead);
        }
    }
);
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionUpdate',
    function (&$arFields) {
        // Изменение руководителей
        if (isset($arFields['OLD_HEAD']) && Loader::includeModule('bizon.main')) {
            $log = new \Bizon\Main\Log('department_change');
            $log->Add('Изменен руководитель отдела #' . $arFields['ID'] . ' с ' . $arFields['OLD_HEAD'] . ' на ' . $arFields['UF_HEAD']);
            \Bizon\Main\Department\Helper::saveHistoryDepartment($arFields['ID']);
        }
    }
);

$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionDelete',
    function (&$arFields) {
        $head = CIntranetUtils::GetStructure()['DATA'][$arFields['ID']]['UF_HEAD'];
        // Изменение руководителей
        if ($head && Loader::includeModule('bizon.main')) {
            $log = new \Bizon\Main\Log('department_change');
            $log->Add('Удален руководитель из отдела #' . $arFields['ID'] . ' - ' . $head);
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
    function (&$arFields) {
        if (Loader::includeModule('bizon.main'))
            \Bizon\Main\Tasks\CheckItem::onAfterCheckListAdd($arFields);
    }
);

$eventManager->addEventHandler(
    'tasks',
    '\Bitrix\Tasks\Internals\Task\CheckList::onAfterUpdate',
    function (&$arFields) {
        if (Loader::includeModule('bizon.main'))
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

// Тестовый доступ к сущностям CRM для дополнительного пользователя на примере Лидов
$eventManager->addEventHandler(
    'crm',
    'onBeforeCrmLeadUpdate',
    function (&$arFields) {
        if (Loader::includeModule('bizon.main')
            && Loader::includeModule('crm')) {
            $log = new Log('crmAccessTest');
            $log->Add('leadBEFOREupdate');
            $log->Add($arFields);

            if (isset($arFields['UF_CRM_ACCESS_USER'])) {
                $prevAccessUser = \Bitrix\Crm\LeadTable::getList([
                    'filter' => ['=ID' => $arFields['ID']],
                    'select' => ['UF_CRM_ACCESS_USER']
                ])->fetch()['UF_CRM_ACCESS_USER'];
                if ($prevAccessUser) {
                    $accessId = \Bizon\Main\CrmAccess\Model\CrmEntityPermsTable::getList([
                        'filter' => [
                            'ENTITY' => 'LEAD',
                            'ENTITY_ID' => $arFields['ID'],
                            'ATTR' => 'U' . $prevAccessUser,
                        ],
                        'select' => ['ID'],
                    ])->fetch()['ID'];

                    $log->Add('AccessId: ' . $accessId);
                    \Bizon\Main\CrmAccess\Model\CrmEntityPermsTable::delete($accessId);
                }
            }
        }
    }
);

$eventManager->addEventHandler(
    'crm',
    'onAfterCrmLeadUpdate',
    function (&$arFields) {
        if (Loader::includeModule('bizon.main')) {
            $log = new Log('crmAccessTest');
            $log->Add('leadAFTERupdate');
            $log->Add($arFields);

            if (isset($arFields['UF_CRM_ACCESS_USER']) && intval($arFields['UF_CRM_ACCESS_USER']) > 0) {
                $result = \Bizon\Main\CrmAccess\Model\CrmEntityPermsTable::add([
                    'ENTITY' => 'LEAD',
                    'ENTITY_ID' => $arFields['ID'],
                    'ATTR' => 'U' . $arFields['UF_CRM_ACCESS_USER'],
                ]);
                if (!$result->isSuccess()) {
                    $log->Add('Ошибка добавления доступа: ');
                    $log->Add($result->getErrorMessages());
                }
            }
        }
    }
);*/
?>