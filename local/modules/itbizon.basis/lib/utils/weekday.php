<?php

namespace Itbizon\Basis\Utils;

class WeekDay
{
    /**
     * @return array
     * @throws \Exception
     */
    public static function getWeekList(): array
    {
        $dates = [];
        $year = (new \DateTime("now"))->format('Y');
        $week = 1;
        $months = 12;
        for ($i = 1; $i <= $months; $i++) {
            $monthNumber = (strlen($i) !== 2) ? "0$i" : $i;
            $date = (new \DateTime("$year-$monthNumber-01"))->setTime(00, 00, 00);
            $days = (int)$date->format('t'); // total number of days in the month

            $oneDay = new \DateInterval('P1D');
            for ($day = 1; $day <= $days; $day++) {
                $dates[$week][] = clone($date);

                $dayOfWeek = $date->format('l');
                if ($dayOfWeek === 'Sunday') {
                    $week++;
                }
                $date->add($oneDay);
            }
        }

        return $dates;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public static function getCurrentWeek(): int
    {
        $currentDate = (new \DateTime('now'))->setTime(00, 00, 00);
        $weeks = self::getWeekList();
        foreach ($weeks as $weekNumber => $week) {
            foreach ($week as $day) {
                if ($day == $currentDate) {
                    return $weekNumber;
                }
            }
        }
        throw new \Exception('Системная ошибка');
    }

    /**
     * @param int $numberWeek
     * @return string
     * @throws \Exception
     */
    public static function getWeekString(int $numberWeek): string
    {
        $week = self::findWeekByNumber($numberWeek);
        if ($week) {
            $startDate = array_values($week)[0];
            $endDate = end($week);
            return '(' . $startDate->format('d.m') . ' - ' . $endDate->format('d.m') . ')';
        }
        throw new \Exception('Неделя не найдена');
    }

    /**
     * @param int $numberWeek
     * @return array
     * @throws \Exception
     */
    protected static function findWeekByNumber(int $numberWeek): array
    {
        $weeks = self::getWeekList();
        foreach ($weeks as $weekNumber => $week) {
            if ($weekNumber == $numberWeek) {
                return $week;
            }
        }
        throw new \Exception('Недел не найдена');
    }
}