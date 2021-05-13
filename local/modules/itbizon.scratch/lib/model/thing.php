<?php

namespace Itbizon\Scratch\Model;

use Bitrix\Main\Entity;

class ThingTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'itb_scratch_thing';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new Entity\IntegerField('BOX_ID'),
            new Entity\StringField('NAME', array('required' => true)),
            new Entity\StringField('DESCRIPTION'),
            new Entity\DateField('CREATION_DATE'),
            new Entity\IntegerField('CREATOR_ID'), // from \Bitrix\Main\UserTable
            new Entity\FloatField('VALUE'),
            new Entity\BooleanField('IS_TRASH', array('values' => array('N', 'Y'))),
            new Entity\StringField('COMMENT'),
        );
    }

}