<?php

use Bitrix\Main\Loader;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\GridHelper;

use \Bizon\Main\FieldCollector\Model\DealFieldTable;
use \Bizon\Main\FieldCollector\Model\DealFieldValueTable;

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}

if(!Loader::includeModule('crm')) {
    throw new Exception('Ошибка подключения модуля crm');
}
if(!Loader::includeModule('bizon.main')) {
    throw new Exception('Ошибка подключения модуля bizon.main');
}


/**
 * Class CITBFieldcollectorDealfieldValues
 */
//class CITBFieldcollectorDealfieldValues extends Complex
class CITBFieldcollectorDealfieldValues extends Simple
{
    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            // Формируем роуты
            /*
            $this->initRoute(
                [
                    'list' => '',
                    'add' => 'add/',
                    'edit' => 'edit/#ID#/',
                    'config' => 'config/',
                ],
                'list'
            );
            $this->getRoute()->run();
            // */

            // DEBUG
            //echo "\n<br> Params = ". print_r($this->arParams, True)."<br>"; // fixme

            //echo "\n<br><pre> Params = ". print_r($this->arParams['HELPER'], True)."<br></pre>"; // fixme

            // DEBUG
            $id = intval($this->arParams['HELPER']->getVariable('ID'));
            //echo "\n<BR>TEST ID = $id"; // fixme

            if (isset($this->arParams['HELPER']))
                $this->setRoute($this->arParams['HELPER']);

            // DEBUG
            if ($this->getRoute() == null)
                throw new Exception('Ошибка получения параметров от родительского класса (null)');


            $id = intval($this->getRoute()->getVariable('ID'));
            echo "\n<BR>DEBUG ID = $id"; // fixme

            $catList = DealFieldTable::getCategoryList();


            // Если роут list
            //if($this->getRoute()->getAction() === 'list') {
            if(True) { // fixme complex - simple
                // Создаем объект грида
                $grid = new GridHelper('itb_fieldcollector_dealfield_values');
                $this->setGrid($grid);
                $grid->setFilter([ // Фильтр грида https://dev.1c-bitrix.ru/api_d7/bitrix/main/systemcomponents/gridandfilter/mainuifilter.php
                    [
                        'id' => 'ID', // Идентификатор
                        'name' => 'ID', // Отображаемое название
                        'type' => 'number', // Тип
                        'default' => true // Отображать по умолчанию
                    ],
                    [
                        'id' => 'DATE_CREATE',
                        'name' => 'Дата внесения',
                        'type' => 'date',
                        'default' => true
                    ],
                    [
                        'id' => 'FIELD_ID',
                        'name' => 'Поле',
                        'type' => 'string',
                        'default' => true,
                    ],
                    [
                        'id' => 'VALUE',
                        'name' => 'Значение',
                        'type' => 'string',
                        'default' => true,
                    ],
                    /*
                    [
                        'id' => 'CATEGORY_ID',
                        'name' => 'Направление',
                        'type'    => 'list',
                        'items'   => $catList,
                        'default' => true,
                    ], // */
                ])->setColumns([ //Колонки грида
                    [
                        'id' => 'ID', // Идентификатор
                        'name' => 'ID', // Отображаемое название
                        'sort' => 'ID', // По какому столбцу в БД сортировать (false - если сортировка запрещена)
                        'default' => true, // Отображать по умолчанию
                    ],
                    [
                        'id' => 'FIELD_ID',
                        'name' => 'Поле',
                        'sort' => 'FIELD_ID',
                        'default' => true,
                    ],
                    [
                        'id' => 'VALUE',
                        'name' => 'Значение',
                        'sort' => 'VALUE',
                        'default' => true,
                    ],
                    [
                        'id' => 'DATE_CREATE',
                        'name' => 'Дата',
                        'sort' => 'DATE_CREATE',
                        'default' => true,
                    ],
                    [
                        'id' => 'CATEGORY_ID',
                        'name' => 'ID категории',
                        'sort' => 'CATEGORY_ID',
                        'default' => false,
                    ],
                    [
                        'id' => 'DEAL_TITLE',
                        'name' => 'Сделка',
                        'sort' => 'DEAL_ID',
                        'default' => true,
                    ],
                ]);

                //Формируем фильтр
                $filter = $grid->getFilterData();
                //при необходимости полученный массив можно модифицировать

                // Делаем выборку
                $result = DealFieldValueTable::getList(
                    [
                        'limit' => $grid->getNavigation()->getLimit(), // Ограничение выборки
                        'offset' => $grid->getNavigation()->getOffset(), // Смещение
                        'order' => $grid->getSort(), // Сортировка SORT
                        'filter' => $filter, // Фильтр WHERE
                        'count_total' => true, // Нужно чтобы не получать отдельным запросом количество записей по фильтру
                        'select'=> ['*', 'DEAL_TITLE' => 'DEAL.TITLE'], // Какие поля выбирать SELECT
                    ]
                );
                while($item = $result->fetchObject()) {
                    // Добавляем строку для каждого элемента
                    $grid->addRow(
                        [
                            'data' => [ // Тут живут значения полей элемента, допустимо пихать html
                                'ID' => $item->getId(),
                                'FIELD_ID' => $item->getFieldId(),
                                'VALUE' => $item->getValue(),
                                'DATE_CREATE' => $item->getDateCreate(),
                                'DEAL_ID' => $item->getDealId(),
                                //'DEAL_TITLE' => ($item->getDeal()) ? ($item->getDeal()->getTitle()) : ($item->getDealId()),
                                'DEAL_TITLE' => ($item->getDeal())
                                    ? (self::getDealLink($item->getDealId(), $item->getDeal()->getTitle()))
                                    : ($item->getDealId()),

                            ],
                        ]
                    );
                }
                $grid->getNavigation()->setRecordCount($result->getCount()); // Устанавливаем количество выбранных записей
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage()); // Фиксируем ошибку в коллекции ошибок компонента
            $this->addError($e->getMessage().' - '. $e->getTraceAsString()); // fixme
        }
        // Подключаем шаблон компонента
        $this->IncludeComponentTemplate();
    }

    public static function getDealLink(int $deal_id, string $deal_title): string
    {
        return '<A href="'.self::getDealUrl($deal_id).'">'.$deal_title.'</A>';
    }

    public static function getDealUrl(int $deal_id): string
    {
        return '/crm/deal/details/'.$deal_id.'/';
    }

}