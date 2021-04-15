<?php

namespace Itbizon\Finance\Model;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\EntityError;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use DateTime;
use Exception;
use Itbizon\Finance\Period;
use Itbizon\Finance\Model\RequestTemplateTable;

Loc::loadMessages(__FILE__);

/**
 * Class PeriodTable
 * @package Itbizon\Finance\Model
 */
class PeriodTable extends DataManager
{
    const STATUS_DISTRIBUTION_PROCEEDS = 0;
    const STATUS_ALLOCATION_COSTS = 1;
    const STATUS_AGREEMENT = 2;
    const STATUS_CLOSED = 3;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.PERIOD_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_period';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_PERIOD';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return Period::class;
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.PERIOD.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\DatetimeField(
                'DATE_START',
                [
                    'title' => Loc::getMessage('ITB_FIN.PERIOD.DATE_START'),
                    'required' => true,
                ]
            ),
            new Fields\DatetimeField(
                'DATE_END',
                [
                    'title' => Loc::getMessage('ITB_FIN.PERIOD.DATE_END'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'STATUS',
                [
                    'title' => Loc::getMessage('ITB_FIN.PERIOD.STATUS'),
                    'required' => true,
                    'default_value' => self::STATUS_DISTRIBUTION_PROCEEDS
                ]
            ),
        ];
    }

    /**
     * @return int
     */
    public static function getStartWeek(): int
    {
        try {
            return intval(Option::get('itbizon.finance', 'startWeek', false));
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * @return string[]
     */
    public static function getWeekDayNames(): array
    {
        return [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday",
        ];
    }

    /**
     * @return string[]
     */
    public static function getWeekDayNamesRu(): array
    {
        return [
            "Понедельник",
            "Вторник",
            "Среда",
            "Четверг",
            "Пятница",
            "Суббота",
            "Воскресенье",
        ];
    }

    /**
     * @return string|null
     */
    public static function getStartWeekName()
    {
        $weekNames = self::getWeekDayNames();
        $startWeek = self::getStartWeek();
        return (isset($weekNames[$startWeek]) ? $weekNames[$startWeek] : $weekNames[0]);
    }

    /**
     * @param string $time
     * @return array|int[]
     */
    public static function parseTime(string $time): array
    {
        $array = explode(':', $time);
        if(count($array) == 2) {
            return [
                max(0, min(23, intval($array[0]))),
                max(0, min(59, intval($array[1])))
            ];
        }
        return [0, 0];
    }

    /**
     * @return array
     */
    public static function getStartTime(): array
    {
        $time = '00:00';
        try {
            $time = Option::get('itbizon.finance', 'startTime', '00:00');
        } catch (Exception $e) {

        }
        return self::parseTime($time);
    }

    /**
     * @return DateTime[]
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getNextPeriod(): array
    {
        $lastPeriod = self::getLast();
        if($lastPeriod) {
            $begin = (new DateTime())->setTimestamp($lastPeriod->getDateEnd()->getTimestamp())->modify('+1 second');
        } else {
            $begin = new DateTime();
        }
        $end = self::getPeriodEnd($begin);
        return [$begin, $end];
    }

    /**
     * @param DateTime $begin
     * @return DateTime
     */
    public static function getPeriodEnd(DateTime $begin): DateTime
    {
        $startWeekName = self::getStartWeekName();
        list($startHours, $startMinutes) = self::getStartTime();

        $end = (clone $begin)->modify("{$startWeekName} this week")->setTime($startHours, $startMinutes, -1, 0);
        if($end < $begin) {
            $end->modify('+7 days');
        }
        return $end;
    }

    /**
     * @return EntityObject|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getLast()
    {
        return self::getList([
            'limit' => 1,
            'order' => [
                'DATE_END' => 'DESC',
            ]
        ])->fetchObject();
    }

    /**
     * @param array $data
     * @return AddResult
     */
    public static function add(array $data)
    {
        $result = new AddResult();

        try {
            $periodsLast = self::getList([
                'filter' => [
                    '>=DATE_START' => [
                        $data['DATE_START'],
                        $data['DATE_END'],
                    ],
                    '<=DATE_END' => [
                        $data['DATE_START'],
                        $data['DATE_END'],
                    ],
                ]
            ])->fetch();

            if ($periodsLast)
                $result->addError(new EntityError(Loc::getMessage('ITB_FIN.PERIOD.ERROR.DATE_EXISTS')));

            $periodsLast = self::getLast();
            if ($periodsLast) {
                /**@var $lastPeriodEnd \Bitrix\Main\Type\DateTime */
                $lastPeriodEnd = (new DateTime((string)$periodsLast->getDateEnd()))->modify('+1 second');
                $newPeriodBegin = new DateTime((string)$data['DATE_START']);
                if ($lastPeriodEnd->getTimestamp() !== $newPeriodBegin->getTimestamp())
                    $result->addError(new EntityError(str_replace('#DATE#', $data['DATE_START']->format('d.m.Y H:i:s'), Loc::getMessage('ITB_FIN.PERIOD.ERROR.DATE_START_INVALID'))));
                if($periodsLast->getStatus() !== PeriodTable::STATUS_CLOSED)
                    $result->addError(new EntityError(Loc::getMessage('ITB_FIN.PERIOD.ERROR.LAST_PERIOD_OPEN')));
            }

            if (!$result->isSuccess(true)) {
                return $result;
            }

            $result = parent::add($data);
            
            if($result->isSuccess())
            {
                $list = RequestTemplateTable::getList([
                    'filter'=>[
                        '=ACTIVE'=>'Y'
                    ]
                ]);
                while($template = $list->fetchObject())
                    $template->createRequest();
            }
            return $result;
            
        } catch (Exception $e) {
            $result->addError(new EntityError($e->getMessage()));
        }
        return $result;
    }

    /**
     * @param DateTime $date
     * @return EntityObject
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getByDate(DateTime $date): ?Period
    {
        return self::getList([
            'limit' => 1,
            'filter' => [
                '<=DATE_START' => $date->format('d.m.Y H:i:s'),
                '>=DATE_END' => $date->format('d.m.Y H:i:s'),
            ]
        ])->fetchObject();
    }

    /**
     * @param int $statusId
     * @return array|string
     */
    public static function getStatus($statusId = null)
    {
        $types = [
            self::STATUS_DISTRIBUTION_PROCEEDS => Loc::getMessage('ITB_FIN.PERIOD.STATUS.DISTRIBUTION_PROCEEDS'),
            self::STATUS_ALLOCATION_COSTS => Loc::getMessage('ITB_FIN.PERIOD.STATUS.ALLOCATION_COSTS'),
            self::STATUS_AGREEMENT => Loc::getMessage('ITB_FIN.PERIOD.STATUS.AGREEMENT'),
            self::STATUS_CLOSED => Loc::getMessage('ITB_FIN.PERIOD.STATUS.CLOSED'),
        ];

        if ($statusId !== null)
            return isset($types[$statusId]) ? $types[$statusId] : Loc::getMessage('ITB_FIN.PERIOD.STATUS.UNKNOWN');
        else
            return $types;
    }
}
