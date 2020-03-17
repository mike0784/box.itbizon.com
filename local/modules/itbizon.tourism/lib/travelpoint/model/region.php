<?php


namespace Itbizon\Tourism\TravelPoint\Model;


use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields;

class RegionTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_tourism_region';
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
                'DEPARTMENT_ID',
                [
                    'required' => true,
                ]
            ),
        ];
    }
}