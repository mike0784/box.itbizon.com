<?php


namespace Itbizon\Finance\Model;

use Bitrix\Main\Entity;
use \Bitrix\Main\ArgumentException;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\ObjectPropertyException;
use \Bitrix\Main\ORM\Fields;
use \Bitrix\Main\ORM\Data\DataManager;
use \Bitrix\Main\ORM\Objectify\EntityObject;
use \Bitrix\Main\ORM\Query\Result;
use \Bitrix\Main\SystemException;
use \Itbizon\Finance\OperationCategory;

Loc::loadMessages(__FILE__);

/**
 * Class OperationCategoryTable
 * @package Itbizon\Finance\Model
 */
class OperationCategoryTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTitle()
    {
        return Loc::getMessage('ITB_FIN.OPERATION_CATEGORY_ENTITY_TITLE');
    }

    /**
     * @return string
     */
    public static function getTableName()
    {
        return 'itb_finance_operation_category';
    }

    /**
     * @return EntityObject|string
     */
    public static function getObjectClass()
    {
        return OperationCategory::class;
    }

    /**
     * @return array
     * @throws SystemException
     */
    public static function getMap()
    {
        return [
            new Fields\IntegerField(
                'ID',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_CATEGORY.ID'),
                    'primary' => true,
                    'autocomplete' => true
                ]
            ),
            new Fields\StringField(
                'NAME',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_CATEGORY.NAME'),
                    'required' => true,
                ]
            ),
            new Fields\BooleanField(
                'ALLOW_INCOME',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_CATEGORY.ALLOW_INCOME'),
                    'required' => true,
                    'values' => [
                        'N',
                        'Y',
                    ],
                ]
            ),
            new Fields\BooleanField(
                'ALLOW_OUTGO',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_CATEGORY.ALLOW_OUTGO'),
                    'required' => true,
                    'values' => [
                        'N',
                        'Y',
                    ],
                ]
            ),
            new Fields\BooleanField(
                'ALLOW_TRANSFER',
                [
                    'title' => Loc::getMessage('ITB_FIN.OPERATION_CATEGORY.ALLOW_TRANSFER'),
                    'required' => true,
                    'values' => [
                        'N',
                        'Y',
                    ],
                ]
            ),
        ];
    }

    /**
     * @return Result|EO_OperationCategory_Result
     * @throws SystemException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     */
    public static function getIncomeList()
    {
        return self::getList([
            'filter' => ['ALLOW_INCOME' => 'Y'],
            'order' => ['NAME' => 'ASC']
        ]);
    }

    /**
     * @return Result|EO_OperationCategory_Result
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getOutgoList()
    {
        return self::getList([
            'filter' => ['ALLOW_OUTGO' => 'Y'],
            'order' => ['NAME' => 'ASC']
        ]);
    }

    /**
     * @return Result|EO_OperationCategory_Result
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getTransferList()
    {
        return self::getList([
            'filter' => ['ALLOW_TRANSFER' => 'Y'],
            'order' => ['NAME' => 'ASC']
        ]);
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

            $currentUser = \Bitrix\Main\Engine\CurrentUser::get();
            if (!$currentUser || !$currentUser->isAdmin())
                throw new \Exception();

            if (OperationTable::getList([
                    'filter' => [
                        'CATEGORY_ID' => $id,
                    ]
                ])->fetchObject() !== null) {
                throw new \Exception(Loc::getMessage('ITB_FIN.OPERATION_CATEGORY.DELETE_ERROR'));
            }


        } catch (\Exception $e) {
            $result->addError(new Entity\EntityError($e->getMessage()));
        }

        return $result;
    }
}