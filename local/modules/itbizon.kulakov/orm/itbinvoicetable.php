<?php

namespace TestModule\Tables;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type\DateTime;

class ItbInvoiceTable extends Entity\DataManager
{
    const TABLE_NAME = 'itb_invoice';

    /**
     *
     */
    public static function getTableName()
    {
        return self::TABLE_NAME;
    }

    /**
     *
     */
    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
            )),
            new Entity\StringField('TITLE', array(
                'required' => true,
            )),
            new Entity\DatetimeField('DATE_CREATE', array(
                'default_value' => new DateTime,
            )),
            new Entity\IntegerField('CREATOR_ID', array(
                'required' => true,
            )),
            new Entity\ReferenceField(
                'CREATOR',
                '\Bitrix\Main\UserTable',
                array('=this.CREATOR_ID' => 'ref.ID')
            ),
            new Entity\IntegerField('AMOUNT', array(
                'required' => true,
                'default_value' => 0,
            )),
            new Entity\TextField('COMMENT'),
        );
    }

    public static function onBeforeAdd()
    {
        \TestModule\Log::write("Добавление накладной");
    }

    public static function OnAfterAdd()
    {
        \TestModule\Log::write("Накладная успешно добавлена");
    }

    public static function OnBeforeDelete()
    {
        \TestModule\Log::write("Удаление накладной");
    }

    public static function OnAfterDelete ()
    {
        \TestModule\Log::write("Накладная успешно удалена");
    }
}