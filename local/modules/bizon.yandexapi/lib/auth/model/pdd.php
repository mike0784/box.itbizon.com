<?php

namespace Bizon\Yandexapi\Auth\Model;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\Type\Date;

/**
 * PDO Токен Яндекс API
 * @package Itbizon\Yandexapi\Auth\Model
 */
class PDDTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_yandexapi_pdd';
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
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\StringField(
                'TOKEN',
                [
                    'required' => true
                ]
            ),
            new Fields\DateField(
                'LIFETIME',
                [
                    'required' => true
                ]
            ),
        ];
    }
}
