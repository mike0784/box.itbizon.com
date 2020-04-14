<?php

namespace Itbizon\Template\SystemFines\EventHandlers;

use Bitrix\Crm\Agent\Notice\Notification;

class FineHandler
{
    public static function OnAfterAddFineHandler(\Bitrix\Main\Event $event)
    {

        $fine = $event->getParameters();
        $sendUserId = $fine['TARGET_ID'];
        $type = ($fine['VALUE'] > 0) ? 'бонус' : 'штраф';

        $notify = new Notification();
        $notify->addTo((int)$sendUserId)
            ->withMessage('Вам назначили ' . $type . ' в размере ' . $fine['VALUE'] / 100)
            ->send();
    }

    public static function OnAfterDeleteFineHandler(\Bitrix\Main\Event $event)
    {
        $pathToLog = __DIR__ . '/../logs/logs.txt';
        $prefix = '';
        $mode = 'w';
        if (file_exists($pathToLog)) {
            $prefix = "\n";
            $mode = 'a';
        }
        $handle = fopen($pathToLog, $mode) or die('Cannot open file:  ');
        $data = $prefix . 'Штраф или бонус удален';
        fwrite($handle, $data);
        fclose($handle);
    }
}

?>