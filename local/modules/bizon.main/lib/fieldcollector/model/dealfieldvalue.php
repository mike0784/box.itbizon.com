<?php

namespace Bizon\Main\FieldCollector\Model;

use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;
use \Bitrix\Main\ORM\Fields\DatetimeField;
use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\Type\DateTime;
use \Bitrix\Crm\DealTable;
use \Bitrix\Main\ORM\Fields\Relations\Reference;
use \Bitrix\Main\ORM\Query\Join;
use \Bitrix\Crm\Category\Entity\DealCategoryTable;

use Bizon\Main\FieldCollector\DealFieldValue;

class DealFieldValueTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_state_collector_deal_field_value';
    }

    public static function getObjectClass()
    {
        return DealFieldValue::class;
    }

    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'title' => 'ID записи',
                    'primary' => true,
                    'autocomplete' => true
                ]),
            new DateTimeField(
                'DATE_CREATE',
                [
                    'title' => 'Дата регистрации',
                    'required' => true,
                    'default_value' => new DateTime(),
                ]
            ),
            new StringField(
                'FIELD_ID',
                [
                    'title' => 'Код поля',
                    'required' => true
                ]
            ),
            (new Reference(
                'FIELD',
                DealFieldTable::getEntity(),
                Join::on('this.FIELD_ID', 'ref.FIELD_ID')
            ))->configureJoinType('left'),
            new IntegerField(
                'DEAL_ID',
                [
                    'title' => 'ID сделки',
                    'required' => false,
                ]
            ),
            (new Reference(
                'DEAL',
                DealTable::getEntity(),
                Join::on('this.DEAL_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new StringField(
                'VALUE',
                [
                    'required' => true
                ]
            ),

        ];
    }
}