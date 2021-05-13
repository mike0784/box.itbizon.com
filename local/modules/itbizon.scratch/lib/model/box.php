<?php

namespace Itbizon\Scratch\Model;

use Bitrix\Main\Entity;

class BoxTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'itb_scratch_box';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\StringField('TITLE', array('required' => true)),
            new Entity\DateField('CREATION_DATE'),
            new Entity\IntegerField('CREATOR_ID'), // from \Bitrix\Main\UserTable
            new Entity\FloatField('AMOUNT'),
            new Entity\IntegerField('COUNT'),
            new Entity\StringField('COMMENT'),
        );
    }

}