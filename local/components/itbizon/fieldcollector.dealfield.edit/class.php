<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;

use Itbizon\Service\Component\Simple;
use Bizon\Main\FieldCollector\Model\DealFieldTable;
use Bizon\Main\FieldCollector\DealField;

use \Bitrix\Crm\DealTable;

use \Bitrix\Crm\Category\DealCategory;

use Itbizon\Service\Component\RouterHelper;

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}

if (!Loader::includeModule('crm'))
    throw new Exception('Ошибка подключения модуля crm');

/**
 * Class CITBFieldcollectorDealfieldEdit
 */
class CITBFieldcollectorDealfieldEdit extends Simple
{
    protected ?DealField $item;

    public array $catList;
    public array $fieldTypeList;
    public $dealCategoryId;
    public $dealFieldId;


    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            // Берем роуты из параметров (их должен передать родительский роутер)

            //DEBUG
            if (!isset($this->arParams['HELPER'])){
                $this->arParams['HELPER'] =
                    new RouterHelper($this,
                        [
                            'list' => '',
                            'add' => 'add/',
                            'edit' => 'edit/#ID#/',
                            'config' => 'config/',
                        ],
                        'list'
                    );
            }

            // DEBUG
            //echo "\n<br>classes list<pre>" . print_r(get_declared_classes(), True) . "</pre>"; // fixme

            //echo  "<br>lang = ".LANGUAGE_ID."<br>"; // fixme

            //echo "\n<br>arResult<pre>".print_r($this->arResult, True); // fixme


            $a = \Bitrix\Crm\Category\DealCategory::getAllIDs(); // fixme
            //echo "\n<br>deal category list<pre>".print_r($a, True)."</pre>"; // fixme

            $s = \Bitrix\Crm\Category\DealCategory::getName(1);
            //echo "Name = $s <br>"; // fixme







            $this->setRoute($this->arParams['HELPER']);

            // Получаем id элемента из роута
            $id = intval($this->getRoute()->getVariable('ID'));
            //echo "\n<BR>DEBUG ID = $id"; // fixme

            // DEBUG fixme
            //echo "\n<BR>DEBUG ID = 1";
            //$id=1;

            if (!$id) {
                throw new Exception('Элемент не найден');
            }


            // category list
            /*
            $catRes = \Bitrix\Crm\Category\Entity\DealCategoryTable::getList();
            $catList = [];
            while($cat = $catRes->fetchObject()){
                //echo "\n<br>cat<pre>" . print_r($cat, True) . "</pre>"; // fixme
                $catList[$cat->getId()] = $cat->getName();
            }
            //echo "\n<br>cat list<pre>" . print_r($catList, True) . "</pre>"; // fixme

            $this->catList = $catList;
            // */

            $this->catList = DealFieldTable::getCategoryList();


            /*
            // fields list
            $fieldRes = DealTable::getMap();
            //$fieldMap = $catRes->fetchObject();
            //echo "\n<br>deal map<pre>" . print_r($fieldRes, True) . "</pre>"; // fixme

            echo "\n<br>deal map<pre>"; // fixme

            foreach($fieldRes as $k => $v){

                echo "\n<br>----------------------------------------";
                echo "<br>".$k." (".gettype($v).")  "." = ".print_r($v, True) ; // fixme


            }
            echo "</pre>"; // fixme
            // */


            $this->fieldTypeList = [];
            //$dealFieldList = self::getDealFields();

            $dealFieldList = DealFieldTable::getDealFields();

            // from documentation
            /*
            $rsData = CUserTypeEntity::GetList( array(), array('ENTITY_ID' => 'CRM_DEAL') );
            while($arRes = $rsData->Fetch())
            {
                echo $arRes["FIELD_NAME"].""; // вывод названия пользовательского поля
                echo "<pre>"; print_r($arRes); echo "</pre>"; // вывод массива значений
            }
            // */


            //echo "\n<br>deal map object<pre>"; // fixme


            //echo "<br> uf list = ".print_r($ufList, True)."<br>\n"; // fixme

            //echo "</pre>"; // fixme

            //$ufList = self::getDealUserFields();
            $ufList = DealFieldTable::getDealUserFields();

            $this->fieldTypeList = array_merge($this->fieldTypeList, $dealFieldList);
            $this->fieldTypeList = array_merge($this->fieldTypeList, $ufList);

            // Ищем запись
            $this->item = DealFieldTable::getByPrimary($id)->fetchObject();
            if (!$this->item) {
                throw new Exception('Элемент не найден');
            }

            $this->dealCategoryId = $this->item->getCategoryId();
            $this->dealFieldId = $this->item->getFieldId();



            // Объект запроса
            $request = Application::getInstance()->getContext()->getRequest();
            if ($request->getPost('save') === 'Y') { // Проверяем что отправлена форма
                // Массив полей из запроса
                $data = $request->getPost('DATA');

                echo "\n<br>post<pre>" . print_r($_POST, True) . "</pre>"; // fixme

                //*

                // Редактируем запись
                $this->getItem()->setCategoryId($data['CATEGORY_ID']);
                $this->getItem()->setFieldId($data['FIELD_ID']);


                $result = $this->getItem()->save();
                if (!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }

                // Закрываем сайдслайдер (делаем гет запрос компонента самого на себя)
                $uri = (new Uri($this->getRoute()->getUrl('edit', ['ID' => $id])));
                if($this->getRoute()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
                die();

                // */               // fixme
            }
        } catch (Exception $e) {
            echo "\n<br>Exception ".$e->getMessage().' - '. $e->getTraceAsString(); // fixme

            $this->addError($e->getMessage()); // Фиксируем ошибку в коллекции ошибок компонента
        }
        // Подключаем шаблон компонента
        $this->IncludeComponentTemplate();
    }

    /**
     * @return DealField|null
     */
    public function getItem(): ?DealField
    {
        return $this->item;
    }


    /*
    public function getUserFields()
    {
        static $fields = null;
        if (!$fields) {
            $fields = (new \CUserTypeManager())->GetUserFields(Finance\Model\OperationTable::getUfId(), 0, Bitrix\Main\Application::getInstance()->getContext()->getLanguage());
        }
        return $fields;
    }
    // */

    /**
     * @return string[]
     */
    /*
    public function getDealFields()
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
    // */

    /*
    public function getDealUserFields()
    {

        $ufList = [];

        $dealsUF = Bitrix\Crm\UserField\UserFieldManager::getUserFieldEntity(CCrmOwnerType::Deal)->GetFields();
        foreach ($dealsUF as $fid => $field) {
            //if($field['USER_TYPE_ID'] != 'file')
            //    unset($dealsUF[$fid]);

            //echo "$fid => ".print_r($field, True)."<br>";


            $ufTitleRes = Bitrix\Main\UserFieldLangTable::getList( [
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

            //$ufList[] = [
            //    'ID' => $field["ID"],
            //    'FIELD_TYPE' => $field['FIELD_NAME'],
            //    'FIELD_NAME' => $ufTitleStr,
            //];

            //$ufList[$field['FIELD_NAME']] = $ufTitleStr;
            $ufList[$field['FIELD_NAME']] = $ufTitleStr.' ('.$field['FIELD_NAME'].')';

        }

        return $ufList;

    }
    // */

}

