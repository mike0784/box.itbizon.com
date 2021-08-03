<?php

namespace Bizon\Main\FieldCollector\Model;

use \Bitrix\Main\ORM\Fields\IntegerField;
use \Bitrix\Main\ORM\Fields\StringField;
use \Bitrix\Main\ORM\Data\DataManager;
// use \Bitrix\Crm\Category\Entity\DealCategoryTable;
use \Bitrix\Crm\Category\Entity;
use \Bitrix\Crm\Category\DealCategory;
use \Bitrix\Crm\Category\Entity\DealCategoryTable;
use \Bitrix\Main\ORM\Fields\Relations\Reference;
use \Bitrix\Main\ORM\Query\Join;
//use \Bitrix\Crm\Category\Entity\DealCategoryTable;

//use \Bitrix\Main\Loader;
//use \Bitrix\Crm\DealTable;

//use Bitrix\CRM\General\crm_owner_type;
//if (!Loader::includeModule('crm'))
//    throw new Exception('Ошибка подключения модуля crm');

use Bizon\Main\FieldCollector\DealField;


class DealFieldTable extends DataManager
{
    public static function getTableName()
    {
        return 'itb_state_collector_deal_field';
    }

    public static function getObjectClass()
    {
        return DealField::class;
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
            new StringField(
                'FIELD_ID',
                [
			        'title' => 'Код поля',
                    'required' => true
                ]
            ),
            new IntegerField(
                'CATEGORY_ID',
                [
			        'title' => 'Код категории',
                    'required' => true,
                ]
            ),
            (new Reference(
                'CATEGORY',
                //DealCategoryTable::getEntity(),

                //Bitrix\Crm\Category\Entity\DealCategoryTable::getEntity(),
                \Bitrix\Crm\Category\Entity\DealCategoryTable::getEntity(),
                //Bitrix\Crm\DealCategoryTable::getEntity(),
                Join::on('this.CATEGORY_ID', 'ref.ID')
            ))->configureJoinType('left')
        ];
    }

    // helper functions

    /**
     * @return string[]
     */
    public static function getDealFields(): array
    {
        return [
            //'ID' => 'ID',
            'DATE_CREATE' => 'Дата создания (DATE_CREATE)',
            'DATE_MODIFY' => 'Дата изменения (DATE_MODIFY)',
            'CREATED_BY_ID' => 'Кем создана, ID сотрудника (CREATED_BY_ID)',
            'MODIFY_BY_ID' => 'Кем изменена, ID сотрудника (MODIFY_BY_ID)',
            'ASSIGNED_BY_ID' => 'Отвественный, ID сотрудника (ASSIGNED_BY_ID)',
            'OPENED' => 'Открыта (OPENED)',
            'LEAD_ID' => 'ID лида (LEAD_ID)',
            'COMPANY_ID' => 'ID компании (COMPANY_ID)',
            'CONTACT_ID' => 'ID контакта (CONTACT_ID)',
            'TITLE' => 'Название сделки (TITLE)',
            'PRODUCT_ID' => 'ID товара (PRODUCT_ID)',
            'STAGE_ID' => 'Стадия сделки (STAGE_ID)',
            'CLOSED' => 'Сделка закрыта (CLOSED)',
            'TYPE_ID' => 'ID типа (TYPE_ID)',
            'OPPORTUNITY' => 'Сумма (OPPORTUNITY)',
            'TAX_VALUE' => 'TAX_VALUE',
            'CURRENCY_ID' => 'ID валюты (CURRENCY_ID)',
            'OPPORTUNITY_ACCOUNT' => 'OPPORTUNITY_ACCOUNT',
            'TAX_VALUE_ACCOUNT' => 'TAX_VALUE_ACCOUNT',
            'ACCOUNT_CURRENCY_ID' => 'ACCOUNT_CURRENCY_ID',
            'PROBABILITY' => 'Вероятность (PROBABILITY)',
            'COMMENTS' => 'Комментарий (COMMENTS)',
            'BEGINDATE' => 'Дата начала (BEGINDATE)',
            'CLOSEDATE' => 'Предполагаемая дата закрытия (CLOSEDATE)',
            'EVENT_DATE' => 'Дата события (EVENT_DATE)',
            'EVENT_ID' => 'ID типа события (EVENT_ID)',
            'EVENT_DESCRIPTION' => 'Описание события (EVENT_DESCRIPTION)',
            'EXCH_RATE' => 'EXCH_RATE',
            'LOCATION_ID' => 'ID места (LOCATION_ID)',
            'ORIGINATOR_ID' => 'Привязка (ORIGINATOR_ID)',
            'ORIGIN_ID' => 'ORIGIN_ID',
            'ADDITIONAL_INFO' => 'ADDITIONAL_INFO',
            'QUOTE_ID' => 'QUOTE_ID',
            'CATEGORY_ID' => 'Направление (CATEGORY_ID)',
            'STAGE_SEMANTIC_ID' => 'STAGE_SEMANTIC_ID',
            'IS_NEW' => 'IS_NEW',
            'WEBFORM_ID' => 'Создана CRM-формой (WEBFORM_ID)',
            'SEARCH_CONTENT' => 'SEARCH_CONTENT',
            'IS_RECURRING' => 'IS_RECURRING',
            'IS_RETURN_CUSTOMER' => 'Повторная сделка (IS_RETURN_CUSTOMER)',
            'IS_REPEATED_APPROACH' => 'Повторное обащение (IS_REPEATED_APPROACH)',
            'SOURCE_ID' => 'Источник (SOURCE_ID)',
            'SOURCE_DESCRIPTION' => 'Дополнительно об источнике (SOURCE_DESCRIPTION)',
            'IS_MANUAL_OPPORTUNITY' => 'IS_MANUAL_OPPORTUNITY',
            'ORDER_STAGE' => 'Статус оплаты сделки (ORDER_STAGE)',

            // нет в БД
            //'DEAL_SUMMARY' => 'Сделка (DEAL_SUMMARY)',
            //'ACTIVITY_ID' => 'Дела (ACTIVITY_ID)',

            //'DEAL_CLIENT' => 'Клиент (DEAL_CLIENT)',
            //'SUM' => 'Сумма/Валюта (SUM)',
            //'DELIVERY_STAGE' => 'Статус доставки (DELIVERY_STAGE)',
            //'TRACKING_PATH' => 'Путь клиента (TRACKING_PATH)',

        ];
    }

    public static function getDealUserFields(): array
    {

        $ufList = [];

        $dealsUF = \Bitrix\Crm\UserField\UserFieldManager::getUserFieldEntity(\CCrmOwnerType::Deal)->GetFields();
        foreach ($dealsUF as $fid => $field) {
            //if($field['USER_TYPE_ID'] != 'file')
            //    unset($dealsUF[$fid]);

            //echo "$fid => ".print_r($field, True)."<br>";

            $ufTitleRes = \Bitrix\Main\UserFieldLangTable::getList( [
                'select' => ['*'],
                'filter' => [
                    "USER_FIELD_ID" =>  $field["ID"],
                    "LANGUAGE_ID" => LANGUAGE_ID,

                ]
            ] );
            $ufTitle = $ufTitleRes->fetchObject();
            $ufTitleStr = $field['FIELD_NAME'];
            if (($ufTitle) && ($ufTitle->getEditFormLabel())){
                $ufTitleStr = $ufTitle->getEditFormLabel();
            }

            //$ufList[$field['FIELD_NAME']] = $ufTitleStr;
            $ufList[$field['FIELD_NAME']] = $ufTitleStr.' ('.$field['FIELD_NAME'].')';

        }

        return $ufList;
    }


    public static function getCategoryList(): array
    {
        // category list
        $catRes = \Bitrix\Crm\Category\Entity\DealCategoryTable::getList();
        $catList = [];
        while($cat = $catRes->fetchObject()){
            //echo "\n<br>cat<pre>" . print_r($cat, True) . "</pre>"; // fixme
            $catList[$cat->getId()] = $cat->getName();
        }
        //echo "\n<br>cat list<pre>" . print_r($catList, True) . "</pre>"; // fixme

        return $catList;
    }

}