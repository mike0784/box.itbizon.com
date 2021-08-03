<?php

use Bitrix\Main\Loader;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\GridHelper;
use \Bizon\Main\FieldCollector\Model\DealFieldTable;

//use \Bitrix\Crm\DealTable;
//use \Bitrix\Crm\Category\DealCategory;

if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}

if (!Loader::includeModule('crm'))
    throw new Exception('Ошибка подключения модуля crm');

if (!Loader::includeModule('bizon.main'))
    throw new Exception('Ошибка подключения модуля bizon.main');


/**
 * Class CITBFieldcollectorDealfieldList
 */
class CITBFieldcollectorDealfieldList extends Complex
{
    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            // Формируем роуты
            $this->initRoute(
                [
                    'list' => '',
                    'add' => 'add/',
                    'edit' => 'edit/#ID#/',
                    //'values' => 'values/',
                    'config' => 'config/',
                    'values' => 'values/#ID#/',
                ],
                'list'
            );
            $this->getRoute()->run();

            // DEBUG
            //echo "\n<br> Params = ". print_r($this->arParams, True)."<br>"; // fixme

            $catList = DealFieldTable::getCategoryList();

            // Если роут list
            if($this->getRoute()->getAction() === 'list') {
                // Создаем объект грида
                $grid = new GridHelper('itb_fieldcollector_dealfield_list');
                $this->setGrid($grid);
                $grid->setFilter([ // Фильтр грида https://dev.1c-bitrix.ru/api_d7/bitrix/main/systemcomponents/gridandfilter/mainuifilter.php
                    [
                        'id' => 'ID', // Идентификатор
                        'name' => 'ID', // Отображаемое название
                        'type' => 'number', // Тип
                        'default' => true // Отображать по умолчанию
                    ],
                    [
                        'id' => 'FIELD_ID',
                        'name' => 'Поле',
                        'type' => 'string',
                        'default' => true,
                    ],
                    [
                        'id' => 'CATEGORY_ID',
                        'name' => 'Направление',
                        'type'    => 'list',
                        'items'   => $catList,
                        'default' => true,
                    ],
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
                        'id' => 'CATEGORY_ID',
                        'name' => 'ID категории',
                        'sort' => 'CATEGORY_ID',
                        'default' => false,
                    ],
                    [
                        'id' => 'CATEGORY',
                        'name' => 'Направление',
                        'sort' => 'CATEGORY_ID',
                        'default' => true,
                    ],
                ]);

                //Формируем фильтр
                $filter = $grid->getFilterData();
                //при необходимости полученный массив можно модифицировать

                // Делаем выборку
                $result = DealFieldTable::getList(
                    [
                        'limit' => $grid->getNavigation()->getLimit(), // Ограничение выборки
                        'offset' => $grid->getNavigation()->getOffset(), // Смещение
                        'order' => $grid->getSort(), // Сортировка SORT
                        'filter' => $filter, // Фильтр WHERE
                        'count_total' => true, // Нужно чтобы не получать отдельным запросом количество записей по фильтру
                        'select'=> ['*', 'CATEGORY.NAME'], // Какие поля выбирать SELECT
                    ]
                );
                while($item = $result->fetchObject()) {
                    // Добавляем строку для каждого элемента
                    $grid->addRow(
                        [
                            'data' => [ // Тут живут значения полей элемента, допустимо пихать html
                                'ID' => $item->getId(),
                                'FIELD_ID' => $item->getFieldId(),
                                'CATEGORY_ID' => $item->getCategoryId(),
                                'CATEGORY' => ($item->getCategory()) ? ($item->getCategory()->getName()) : (''),
                            ],
                            'actions' => [ // Тут живет список действий доступных для элемента (контекстное меню). Зачастую список формируется с учетом проверки прав
                                [
                                    //Действие откроет в слайдере компонент редактирования элемента
                                    'text' => 'Редактировать',
                                    'default' => true,
                                    'onclick' => (new JsCode(
                                        'BX.SidePanel.Instance.open(
                                        "' . $this->getRoute()->getUrl('edit', ['ID' => $item->getId()]) . '", 
                                        {
                                            cacheable: false,
                                            width: 600
                                        }
                                    );'
                                    ))->getCode(),
                                ],
                                [
                                    //Действие вызовет js код с параметрами
                                    'text' => 'Удалить',
                                    'default' => true,
                                    'onclick' => (new JsCode('deleteDealfieldItem('.$item->getId().', "' . $grid->getGridId() . '")'))->getCode(),
                                ],
                                [
                                    //Действие откроет журнал для этого поля из таблицы DealFieldValue
                                    'text' => 'История',
                                    'default' => true,
                                    'onclick' => 'document.location.href="' . $this->makeValuesLink($item->getId()) . '";',
                                ],

                            ]
                        ]
                    );
                }
                $grid->getNavigation()->setRecordCount($result->getCount()); // Устанавливаем количество выбранных записей
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage(). ' ' . $e->getTraceAsString()); // Фиксируем ошибку в коллекции ошибок компонента
        }
        // Подключаем шаблон компонента
        $this->IncludeComponentTemplate();
    }

    protected function makeValuesLink($id)
    {
        return $this->arParams['SEF_FOLDER'] . "values/$id/";
    }

}