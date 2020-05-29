<?php

namespace Itbizon\Meleshev\Model;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ORM\Event;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\Entity\BooleanField;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\Type\DateTime;

class AutoTable extends DataManager
{
    const FULL_LOG_FILE_NAME = "/home/bitrix/www/local/modules/itbizon.meleshev/logs/auto_log.txt";
    public static function getTableName()
    {
        return 'itb_auto';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', [
                'primary' => true,
                'autocomplete' => true,
            ]),
            new IntegerField('SHOP_ID', [
                'required' => true
            ]),
            new ReferenceField(
                'SHOP',
                'Itbizon\Meleshev\Shop',
                ['=this.SHOP_ID' => 'ref.ID']
            ),
            new StringField('MARK', [
                'required' => true
            ]),
            new StringField('MODEL', [
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
            new IntegerField('VALUE', [
                'required' => true,
                'validation' => function() {
                    return [
                        function ($value) {
                            if ($value > 0) {
                                return true;
                            } else {
                                return 'Стоимость авто должна быть больше нуля';
                            }
                        }
                    ];
                }
            ]),
            new BooleanField('IS_USED', [
                'required' => true,
                'values' => ['N', 'Y']
            ]),
            new StringField('COMMENT')
        );
    }

    public static function onAfterAdd(Event $event)
    {
        $log = fopen(AutoTable::FULL_LOG_FILE_NAME, 'c');

        $data = $event->getParameter("fields");
        $id = $data["ID"];
        fwrite($log, "Конец записи в бд нового авто с идентификатором $id");
        fclose($log);
        $shopId = $data["SHOP_ID"];
        $shopData = ShopTable::getById($shopId)->fetch();
        $shopData["COUNT"]  = ShopTable::getCountOfAllAuto($shopId);
        $shopData["AMOUNT"] = ShopTable::getAmountOfAllAuto($shopId);
        $result = ShopTable::update($shopId, $shopData);
        if ($result->isSuccess()) {
            fwrite($log, "Запись в бд нового авто с идентификатором $id прошла.
            Пересчет AMOUNT и COUNT осуществлён");
        }
    }
}