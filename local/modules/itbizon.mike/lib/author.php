<?php
namespace Itbizon\Mike;

use Bitrix\Main;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;

class AuthorTable extends Main\Entity\DataManager
{
    public static function getTableName(): string
    {
        return 'itb_mike_author';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField('IDAUTHOR', ['primary' => true, 'autocomplete' => true]),
            new Entity\StringField('NAME'),
            new Entity\DatetimeField('CREATEAT', array('default_value' => new DateTime)),
            new Entity\DatetimeField('UPDATEAT', array('default_value' => new DateTime)),
            new Reference('BOOK', BookTable::class, Join::on('this.IDAUTHOR', 'ref.IDAUTHOR')),
        ];
    }
}