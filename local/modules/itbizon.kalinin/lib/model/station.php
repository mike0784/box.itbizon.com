<?php

namespace Itbizon\Kalinin\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Type\Date;
use Itbizon\Kalinin\Log\Logger;
use Station;

class StationTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_station';
    }

    public static function getObjectClass()
    {
        return Station::class;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new Fields\StringField('NAME', [
                'required' => true
            ]),
            new Fields\DateField('DATE_CREATE',
                [
                    'default_value' => new Date
                ]
            ),
            new Fields\IntegerField(
                'CREATOR_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'CREATOR',
                \Bitrix\Main\UserTable::getEntity(),
                Join::on('this.CREATOR_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField('AMOUNT',
                [
                    'required' => true,
                    'default_value' => 0
                ]
            ),
            new Fields\IntegerField('COUNT',
                [
                    'required' => true,
                    'default_value' => 0
                ]
            ),
            new Fields\TextField(
                'COMMENT'
            ),
        ];
    }

    public static function onAfterAdd(Event $event)
    {
        Logger::LogInfo("Добавлена станция");
    }

    public static function onAfterDelete(Event $event)
    {
        Logger::LogInfo("Удалена станция");
    }


}