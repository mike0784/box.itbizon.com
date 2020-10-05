<?php
/*
    WRONG... SHOULD DELETE LATER.
*/

namespace Itbizon\Kalinin\Lib\Model;

use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Type\Date;

class ShipTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_ship';
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
}
