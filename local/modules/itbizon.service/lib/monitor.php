<?php

namespace Itbizon\Service;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Monitor
{
    /**
     * @return string
     */
    public static function agent(): string
    {
        try {
            $documentRoot = $_SERVER['DOCUMENT_ROOT'];
            $oneMbInByte = 1048576;
            $freeSpace = floatval(disk_free_space($documentRoot) / $oneMbInByte);
            $totalSpace = floatval(disk_total_space($documentRoot) / $oneMbInByte);
            $thresholdMb = Option::get('itbizon.service', 'monitor_threshold_hdd');
            $serverName = !empty(SITE_SERVER_NAME) ? SITE_SERVER_NAME : $_SERVER['SERVER_NAME'];
            $alertHdd = ($thresholdMb > $freeSpace);

            if ($alertHdd) {
                $isSend = Option::get('itbizon.service', 'monitor_notify_email_active');
                $emails = trim(Option::get('itbizon.service', 'monitor_notify_email_list'));
                if ($isSend == 'Y' && strlen($emails)) {
                    $emails = explode(',', $emails);
                    $freeSpaceToSend = number_format($freeSpace, 2);
                    $message = Loc::getMessage('ITB_SERV_MONITORING.MAIL.MESSAGE', [
                        '#SERVER_NAME#' => $serverName,
                        '#FREE_SPACE#' => $freeSpaceToSend
                    ]);
                    foreach ($emails as $email) {
                        $sendTo = trim($email);
                        if (!empty($sendTo)) {
                            mail($sendTo, Loc::getMessage('ITB_SERV_MONITORING.MAIL.SUBJECT.TITLE'), $message);
                        }
                    }
                }
            }

            $statistic = Statistic::getInstance();
            $isSend = $statistic->send(Statistic::CMD_MONITOR, [
                'hdd_free_space' => $freeSpace,
                'hdd_total_space' => $totalSpace,
                'alert' => intval($alertHdd),
            ]);

            if (!$isSend) {
                throw new \Exception('Error: ' . $statistic->getLastError());
            }
        } catch (\Exception $e) {
            Log::addDef($e->getMessage(), Log::LEVEL_ERROR);
        }
        return '\\' . __METHOD__ . '();';
    }
}