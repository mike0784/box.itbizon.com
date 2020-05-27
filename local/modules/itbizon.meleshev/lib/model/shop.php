<?php

namespace Itbizon\Meleshev\Model;

use Bitrix\Main\Entity\DataManager as DataManagerAlias;
use Bitrix\Main\Entity\DateField;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Type\DateTime;

class ShopTable extends DataManagerAlias
{
    const FULL_LOG_FILE_NAME = "../logs/shop_log.txt";
    public static function getTableName()
    {
        return 'itb_shop';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new StringField('TITLE', [
                'required' => true
            ]),
            new DateField('DATE_CREATE', [
                'default_value' => new DateTime()
            ]),
            new IntegerField('CREATOR_ID'),
            new ReferenceField(
                'CREATOR',
                '\Bitrix\Main\UserTable',
                ['=this.CREATOR_ID' => 'ref.ID']
            ),
            new IntegerField('AMOUNT', [
                'required' => true,
                'default_value' => 0
            ]),
            new IntegerField('COUNT', [
                'required' => true,
                'default_value' => 0
            ]),
            new StringField('COMMENT')
        );
    }

    public static function getAllAuto($id)
    {
        $parameters = [
            'select' => ['*'],
            'filter' => ['=SHOP_ID' => $id]
        ];

        return AutoTable::getList($parameters)->fetchAll();
    }

    public static function getCountOfAllAuto($id)
    {
        $parameters = [
            'select' => ['*'],
            'filter' => ['=SHOP_ID' => $id],
            'count_total' => 'Y'
        ];

        return AutoTable::getList($parameters)->getCount();
    }

    public static function getAmountOfAllAuto($id)
    {
        $parameters = [
            'select' => ['VALUE'],
            'filter' => ['=SHOP_ID' => $id]
        ];
        $cars = AutoTable::getList($parameters)->fetchAll();
        $amount = 0;
        foreach ($cars as $car) {
            $amount += $car['VALUE'];
        }
        return $amount;
    }
}