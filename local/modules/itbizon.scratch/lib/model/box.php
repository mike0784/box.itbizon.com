<?php

namespace Itbizon\Scratch\Model;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Type\DateTime;
use Itbizon\Scratch\Box;


class BoxTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'itb_scratch_box';
    }

    public static function getObjectClass()
    {
        return Box::class;
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID',
                [
                    'primary' => true,
                    'autocomplete' => true
                ]),
            new Entity\StringField('TITLE',
                [
                    'required' => true
                ]),
            new Entity\DateField('CREATION_DATE',
                [
                    'default_value' => new DateTime()
                ]),
            new Entity\IntegerField('CREATOR_ID',
                [
                    'default_value' => THingTable::getUserId()
                ]), // from \Bitrix\Main\UserTable
            new Entity\FloatField('AMOUNT'),
            new Entity\IntegerField('COUNT'),
            new Entity\StringField('COMMENT'),
        );
    }

    public static function getUserId(): int
    {
        return CurrentUser::get()->getId();
        //return $USER->getId();
    }

}