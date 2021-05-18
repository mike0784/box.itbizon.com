<?php

namespace Itbizon\Scratch\Model;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Data\DataManager;;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Itbizon\Scratch\Model\BoxTable;
use Itbizon\Scratch\Thing;

class ThingTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'itb_scratch_thing';
    }

    public static function getObjectClass()
    {
        return Thing::class;
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID',
                [
                    'primary' => true,
                    'autocomplete' => true
                ]),
            new Entity\IntegerField('BOX_ID'),
            new Entity\StringField('NAME',
                [
                    'required' => true
                ]),
            new Entity\StringField('DESCRIPTION'),
            new Entity\DateField('CREATION_DATE',
                [
                    'default_value' => new DateTime()
                ]),
            new Entity\IntegerField('CREATOR_ID',
                [
                    'default_value' => ThingTable::getUserId()
                ]), // from \Bitrix\Main\UserTable
            new Entity\FloatField('VALUE',
                [
                    'default_value' => 0,
                    'required' => true,
                ]),
            new Entity\BooleanField('IS_TRASH',
                [
                    'values' => array('N', 'Y')
                ]),
            new Entity\StringField('COMMENT'),
            (new Reference(
                'BOX',
                BoxTable::getEntity(),
                Join::on('this.BOX_ID', 'ref.ID')
            ))->configureJoinType('left')

        );
    }

    public static function getUserId(): int
    {
        return CurrentUser::get()->getId();
        //return $USER->getId();
    }


}