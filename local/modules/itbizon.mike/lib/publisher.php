<?php
namespace Itbizon\Mike;

use Bitrix\Main;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\Type\DateTime;
use Main\ORM\Fields\Relations;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Entity;
use Itbizon\Book\BookTable;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;


class PublisherTable extends Main\Entity\DataManager
{
    public static function getTableName(): string
    {
        return 'itb_mike_publisher';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField('IDPUBLISHER', ['primary' => true, 'autocomplete' => true]),
            new Entity\StringField('NAMECOMPANY'),
            new Entity\DatetimeField('CREATEAT', array('default_value' => new DateTime)),
            new Entity\DatetimeField('UPDATEAT', array('default_value' => new DateTime)),
            (new OneToMany('BOOK', BookTable::class, 'IDPUBLISHER')) -> configureJoinType('inner')
        ];
    }
}