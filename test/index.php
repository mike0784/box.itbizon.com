<?php

use Bitrix\Main\Loader;
use Bizon\Main\Tasks\CheckItem;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Тест");

try
{
    function test(bool $state) {
        static $start = false;
        if($state || !$start) {
            $start = microtime(true);
            $result = $start;
        } else {
            $result = microtime(true) - $start;
        }
        return $result;
    }

    function getNextDateByDayNumber(DateTime $date, int $dayNumber)
    {
        $day   = intval($date->format('j'));
        $month = intval($date->format('n'));
        $year  = intval($date->format('Y'));
        $days  = intval($date->format('t'));

        $newDay = max(1, min($days, $dayNumber));

        $newDate = clone $date;
        if($newDay < $day) {
            $month++;
            if($month > 12) {
                $month = 1;
                $year++;
            }
            $newDate->setDate($year, $month, 1);
            $days = intval($newDate->format('t'));
            $newDay = min($days, $dayNumber);
        }

        $newDate->setDate($year, $month, $newDay);
        return $newDate;
    }

    function getNextDateByDayNumber2(DateTime $date, int $dayNumber)
    {
        $day   = intval($date->format('j'));
        $month = intval($date->format('n'));
        $year  = intval($date->format('Y'));
        $days  = intval($date->format('t'));
        $newDay = max(1, min($days, $dayNumber));

        if($newDay < $day) {
            $nextDate = (clone $date)->setDate($year, $month+1, 1);
            $nextDays = intval($nextDate->format('t'));
            $newDay = max(1, min($nextDays, $dayNumber));
            $delta = ($days - $day) + $newDay;
        } else {
            $delta = $newDay - $day;
        }

        return (new DateTime())->setDate($year, $month, $day+$delta);
    }

    $dayNumber = 24;
    $currentDate = new DateTime('2019-12-25 00:00:00');
    $max = 100000;

    $time = microtime(true);
    $i = 0;
    while($i < $max) {
        $newDate = getNextDateByDayNumber2($currentDate, $dayNumber);

        //echo $currentDate->format('d.m.Y H:i:s').' - '.$newDate->format('d.m.Y H:i:s').'<br>';
        $currentDate->modify('+1 month');
        $i++;
    }
    echo '<b>'.(microtime(true) - $time).'</b><br>';

    /*$time = microtime(true);
    $i = 0;
    while($i < $max) {
        $newDate = getNextDateByDayNumber2($currentDate, $dayNumber);

        //echo $currentDate->format('d.m.Y H:i:s').' - '.$newDate->format('d.m.Y H:i:s').'<br>';
        $currentDate->modify('+1 month');
        $i++;
    }
    echo '<b>'.(microtime(true) - $time).'</b><br>';*/
}
catch(Exception $e)
{
    echo '<p>'.$e->getMessage().'</p>';
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");