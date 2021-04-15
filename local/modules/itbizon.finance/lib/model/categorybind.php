<?php


namespace Itbizon\Finance\Model;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\SystemException;
use Itbizon\Finance\CategoryBind;

/**
 * Class CategoryBindTable
 * @package Itbizon\Finance\Model
 */
class CategoryBindTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.CATEGORY_BIND_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_category_bind';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return CategoryBind::class;
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'CATEGORY_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.CATEGORY_BIND.CATEGORY_ID'),
                    'primary' => true,
                ]
            ),
            new Fields\IntegerField(
                'STOCK_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.CATEGORY_BIND.STOCK_ID'),
                    'required' => true,
                ]
            ),
        ];
    }

    /**
     * @param int $categoryId
     * @param int $stockId
     * @return mixed
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public static function upsert(int $categoryId, int $stockId)
    {
        $bind = self::getById($categoryId)->fetchObject();
        if($bind) {
            return $bind
                ->setStockId($stockId)
                ->save();
        } else {
            return (new CategoryBind())
                ->setCategoryId($categoryId)
                ->setStockId($stockId)
                ->save();
        }
    }
}