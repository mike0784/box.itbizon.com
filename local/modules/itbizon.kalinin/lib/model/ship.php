<?php
/*
    WRONG... SHOULD DELETE LATER.
*/

namespace Itbizon\Kalinin\Lib\Model;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\Date;
use Itbizon\Kalinin\Lib\Log\Logger;
use Ship;

class ShipTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_ship';
    }

    public static function getObjectClass()
    {
        return Ship::class;
    }

    /**
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField('ID',
                [
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\IntegerField('STATION_ID',
                [
                    'required' => true
                ]
            ),
            (new Fields\Relations\Reference('STATION',
                ShopTable::class,
                Join::on('this.STATION_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\StringField('NAME',
                [
                    'required' => true
                ]
            ),
            new Fields\StringField('MATERIALS',
                [
                    'required' => true
                ]
            ),
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
            new Fields\IntegerField('VALUE',
                [
                    'required' => true,
                    'validation' => function () {
                        return array(
                            function ($value) {
                                if (is_numeric($value) && $value > 0)
                                {
                                    return true;
                                } else {
                                    return 'Стоимость корабля должна быть больше нуля';
                                }
                            }
                        );
                    }
                ]
            ),
            (new Fields\BooleanField('IS_RELEASED',
                [
                    'required' => true,
                ]
            ))->configureValues('N', 'Y'),
            new Fields\TextField('COMMENT'),
        ];
    }

    public static function onAfterAdd(Event $event)
    {
        Logger::LogInfo("Добавлен корабль");
    }

    public static function onAfterDelete(Event $event)
    {
        Logger::LogInfo("Удалён корабль");
    }
}
