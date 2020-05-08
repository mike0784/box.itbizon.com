<?php

namespace TestModule\Tables;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type\DateTime;

class ItbProductTable extends Entity\DataManager
{
    const TABLE_NAME = 'itb_product';

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
            new Entity\IntegerField('INVOICE_ID', array(
                'required' => true,
            )),
            new Entity\ReferenceField(
                'INVOICE',
                'TestModule\Tables\ItbInvoiceTable',
                array('=this.INVOICE_ID' => 'ref.ID')
            ),
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
            new Entity\IntegerField('VALUE', array(
                'required' => true,
                'validation' => function() {
                   return array(
                       function($value)
                       {
                           return ($value === 0 ? false : true);
                       }
                   );
                },
            )),
            new Entity\IntegerField('COUNT', array(
                'required' => true,
                'validation' => function() {
                    return array(
                        function($value)
                        {
                            return ($value === 0 ? false : true);
                        }
                    );
                },
            )),
            new Entity\TextField('COMMENT'),
        );
    }


    public static function onBeforeAdd()
    {
        \TestModule\Log::write("Добавление товара");
    }

    public static function OnAfterAdd()
    {
        \TestModule\Log::write("Товар успешно добавлен");
    }

    public static function OnBeforeDelete()
    {
        \TestModule\Log::write("Удаление товара");
    }

    public static function OnAfterDelete ()
    {
        \TestModule\Log::write("Товар успешно удален");
    }
}