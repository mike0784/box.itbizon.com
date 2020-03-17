<?php


namespace Itbizon\Tourism\TravelPoint\Model;

use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\ORM\Query\Join;

class CityTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_tourism_city';
    }

    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'primary'      => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'COUNTRY_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'COUNTRY',
                RegionTable::getEntity(),
                Join::on('this.COUNTRY_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'DEPARTMENT_ID',
                [

                ]
            ),
        ];
    }
}