<?php
namespace testModule\test\model;

use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;

class TestTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_test';
    }
    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new StringField(
                'NAME',
                [
                    'required'=>true,
                ]
            ),
        ];
    }
}