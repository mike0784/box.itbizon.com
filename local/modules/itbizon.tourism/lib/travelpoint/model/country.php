<?php


namespace Itbizon\Tourism\TravelPoint\Model;

use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\ORM\Query\Join;

class CountryTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_tourism_country';
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
                'REGION_ID',
                [
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'REGION',
                RegionTable::getEntity(),
                Join::on('this.REGION_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'DEPARTMENT_ID',
                [

                ]
            ),
        ];
    }
}