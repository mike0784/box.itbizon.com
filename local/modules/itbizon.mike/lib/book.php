<?php
namespace Itbizon\Mike;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\Entity;
use Bitrix\Main\Type\DateTime;

class BookTable extends Main\Entity\DataManager
{
    public static function getTableName(): string
    {
        return 'itb_mike_book';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField('ID_BOOK', ['primary' => true, 'autocomplete' => true]),
            new Entity\IntegerField('ID_PUBLISHER'),
            new Entity\IntegerField('ID_AUTHOR'),
            new Entity\StringField('TITLE'),
            new Entity\DatetimeField('CREATE_AT', array('default_value' => new DateTime)),
            new Entity\DatetimeField('UPDATE_AT', array('default_value' => new DateTime)),
            new Reference('PUBLISHER', PublisherTable::class, Join::on('this.ID_PUBLISHER', 'ref.ID_PUBLISHER')),
            new Reference('AUTHOR', AuthorTable::class, Join::on('this.ID_AUTHOR', 'ref.ID_AUTHOR')),
        ];
    }
}

/*class BooksReader extends Itbizon\Mike\Book\BookTable
{

    public static function create(array $fields)
    {
        $BookData = [
            "ID_PUBLISHER" =>$fields["ID_PUBLISHER"],
            "ID_AUTHOR" =>$fields["ID_AUTHOR"],
            "TITLE" => $fields["TITLE"],
        ];
        $result = BooksReader::add($BookData);
        return $result;
    }


    public function edit(array $fields)
    {
        $id = $fields["ID_BOOK"];
        $BookData = [
            "ID_PUBLISHER" =>$fields["ID_PUBLISHER"],
            "ID_AUTHOR" =>$fields["ID_AUTHOR"],
            "TITLE" => $fields["TITLE"],
            "CREATE_AT" => $fields["CREATE_AT"],
        ];
        $result = BooksReader::update($id, $BookData)

        return $result;
    }

    public function delete(int $id)
    {
        $result = BooksReader::delete($id);
        return $result;
    }

    public function select(array $fields)
    {
        $q = new Entity\Query(BookTable::getEntity());
        $q->setSelect(array('*'));
        //$q->setFilter(array('=ID' => 1));
        $result = $q->exec();
    }
}*/