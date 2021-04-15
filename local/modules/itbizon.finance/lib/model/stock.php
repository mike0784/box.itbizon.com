<?php


namespace Itbizon\Finance\Model;


use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\AddResult;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Data\UpdateResult;
use Bitrix\Main\ORM\Fields;
use Bitrix\Main\ORM\Objectify\EntityObject;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use Exception;
use Itbizon\Finance\Stock;

Loc::loadMessages(__FILE__);

/**
 * Class StockTable
 * @package Itbizon\Finance\Model
 */
class StockTable extends DataManager
{
    const STOCK_INCOME = 1;
    const STOCK_MARGIN = 2;
    const STOCK_CORRECT_PROFIT = 3;

    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.STOCK_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_vault';
    }

    /**
     * @return string|null
     */
    public static function getUfId()
    {
        return 'ITB_FIN_VAULT';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return Stock::class;
    }

    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\DatetimeField(
                'DATE_CREATE',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.DATE_CREATE'),
                    'default_value' => new DateTime()
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.NAME'),
                    'required' => true,
                    'validation' => function () {
                        return [
                            new Fields\Validators\UniqueValidator()
                        ];
                    },
                ]
            ),
            new Fields\IntegerField(
                'TYPE',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.TYPE'),
                    'required' => true,
                    'default_value' => VaultTable::TYPE_STOCK
                ]
            ),
            new Fields\IntegerField(
                'RESPONSIBLE_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.RESPONSIBLE_ID'),
                    'required' => true,
                ]
            ),
            (new Fields\Relations\Reference(
                'RESPONSIBLE',
                UserTable::getEntity(),
                Join::on('this.RESPONSIBLE_ID', 'ref.ID')
            ))->configureJoinType('left'),
            new Fields\IntegerField(
                'BALANCE',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.BALANCE'),
                ]
            ),
            new Fields\BooleanField(
                'HIDE_ON_PLANNING',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.HIDE_ON_PLANNING'),
                    'default_value' => false
                ]
            ),
            new Fields\IntegerField(
                'STOCK_GROUP_ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.STOCK_GROUP_ID'),
                    'required' => true,
                ]
            ),
            new Fields\IntegerField(
                'PERCENT',
                [
                    'title' => Loc::getMessage('ITB_FIN.STOCK.PERCENT'),
                    'default_value' => 0,
                    'required' => true,
                ]
            ),
        ];
    }

    /**
     * @param int $groupId
     * @return array|string
     */
    public static function getGroups($groupId = null)
    {
        $groups = [
            self::STOCK_INCOME         => Loc::getMessage('ITB_FIN.STOCK.GROUP.INCOME'),
            self::STOCK_MARGIN         => Loc::getMessage('ITB_FIN.STOCK.GROUP.MARGIN'),
            self::STOCK_CORRECT_PROFIT => Loc::getMessage('ITB_FIN.STOCK.GROUP.CORRECT_PROFIT'),
        ];
        if ($groupId !== null)
            return isset($groups[$groupId]) ? $groups[$groupId] : Loc::getMessage('ITB_FIN.STOCK.GROUP.UNKNOWN');
        else
            return $groups;
    }

    /**
     * @param array $data
     * @return Result
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public static function getList(array $data = [])
    {
        if (!isset($data['select']) || !is_array($data['select']) || empty($data['select']))
            $data['select'] = ['*', 'RESPONSIBLE.LAST_NAME', 'RESPONSIBLE.NAME'];
        $data['filter']['=TYPE'] = VaultTable::TYPE_STOCK;
        return parent::getList($data);
    }

    /**
     * @param array $data
     * @return AddResult
     * @throws Exception
     */
    public static function add(array $data)
    {
        if(isset($data['TYPE']) && $data['TYPE'] != VaultTable::TYPE_STOCK)
            throw new Exception('Vault type deny for stock entity');
        return parent::add($data);
    }

    /**
     * @param mixed $primary
     * @param array $data
     * @return UpdateResult
     * @throws Exception
     */
    public static function update($primary, array $data)
    {
        if(isset($data['TYPE']) && $data['TYPE'] != VaultTable::TYPE_STOCK)
            throw new Exception('Vault type deny for stock entity');
        return parent::update($primary, $data);
    }

    /**
     * @param $event
     * @return Entity\EventResult
     */
    public static function OnBeforeDelete($event)
    {
        $result = new Entity\EventResult;
        $id = $event->getParameter("id");

        try {
            $id = $id["ID"];

            $stock = self::getById($id)->fetchObject();

            if ($stock->getBalance() != 0)
                throw new Exception(Loc::getMessage('ITB_FIN.STOCK.DELETE_ERROR'));

        } catch (Exception $e) {
            $result->addError(new Entity\EntityError($e->getMessage()));
        }
        return $result;
    }

    /**
     * @return Stock
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getTree(): Stock
    {
        $stockCollection = self::getList(['order' => ['STOCK_GROUP_ID' => 'ASC', 'NAME' => 'ASC']])->fetchCollection();

        $incomeStock = Stock::createIncomeStock();
        $marginStock = Stock::createMarginStock();
        $correctProfitStock = Stock::createCorrectProfitStock();
        foreach($stockCollection as $stock) {
            if($stock->getStockGroupId() === self::STOCK_INCOME) {
                $incomeStock->addChild($stock);
            }
            else if($stock->getStockGroupId() === self::STOCK_MARGIN) {
                $marginStock->addChild($stock);
            }
            else if($stock->getStockGroupId() === self::STOCK_CORRECT_PROFIT) {
                $correctProfitStock->addChild($stock);
            }
        }
        $incomeStock->addChild($marginStock);
        $marginStock->addChild($correctProfitStock);

        return $incomeStock;
    }
}