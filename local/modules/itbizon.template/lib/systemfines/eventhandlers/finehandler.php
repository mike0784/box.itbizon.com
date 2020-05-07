<?php

namespace Itbizon\Template\SystemFines\EventHandlers;

use Bitrix\Main\IO\File;
use Bitrix\Main\IO\FileSystemEntry;
use CIMMessenger;
use CIMNotify;
use CModule;

class FineHandler
{
    public static function OnAfterAddFineHandler(\Bitrix\Main\Event $event)
    {

        if (IsModuleInstalled("im") && CModule::IncludeModule("im")) {
            $fine = $event->getParameters();
            $sendUserId = (int)$fine['TARGET_ID'];
            $fromUser = (int)$fine['CREATOR_ID'];
            $type = ($fine['VALUE'] > 0) ? 'бонус' : 'штраф';
            $message = 'Вам назначили ' . $type . ' в размере ' . $fine['VALUE'] / 100;

            $arMessageFields = array(
                // получатель
                "TO_USER_ID" => $sendUserId,
                // отправитель
                "FROM_USER_ID" => $fromUser,
                // тип уведомления
                "NOTIFY_TYPE" => IM_NOTIFY_FROM,
                // модуль запросивший отправку уведомления
                "NOTIFY_MODULE" => "itbizone.template",
                // текст уведомления на сайте
                "NOTIFY_MESSAGE" => $message,
                "NOTIFY_TITLE" => mb_strtoupper($type),
            );
            CIMNotify::Add($arMessageFields);
        }
    }

    public static function OnAfterDeleteFineHandler(\Bitrix\Main\Event $event)
    {
        $pathToLog = __DIR__ . '/../logs/logs.txt';
        File::putFileContents($pathToLog, "\n Штраф или бонус удален", File::APPEND);
    }
}

?>