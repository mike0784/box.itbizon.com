<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\BookTable;
use ItBizon\Mike\BookAdd;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\GridHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookViewPage extends Complex
{
    protected $result = array();
    public $gridId = 'mike_book';
    public $gridRows = array();
    public $gridColumns = [
        ['id' => 'IDBOOK', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'PUBLISHER', 'name' => 'Наименование организации', 'sort' => 'DATE', 'default' => true],
        ['id' => 'AUTHOR', 'name' => 'Автор', 'sort' => 'DATE', 'default' => true],
        ['id' => 'TITLE', 'name' => 'Наименование книги', 'sort' => 'TITLE', 'default' => true],
        ['id' => 'CREATE_AT', 'name' => 'Дата создания', 'sort' => 'CREATE_AT', 'default' => true],
        ['id' => 'UPDATE_AT', 'name' => 'Дата обнавления', 'sort' => 'UPDATE_AT', 'default' => true],
    ];
    public $gridNav;
    public $listAuthor = array();
    public $listPublisher = array();
    public $grid;
    /**
     * Проверка подключения модуля
     */
    public function _checkModules()
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('ITB_MIKE_BOOK_VIEW_ERROR_INCLUDE_MODULE'));
        }

        if (!Loader::includeModule('itbizon.service')) {
            throw new Exception("Модуль itbizon.service не найден");
        }
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this->_checkModules();

        $this->initRoute(
            [
                'view' => '',
                'book.add' => 'book.add/',
                'book.update' => 'book.update/#ID#/',
                'author.view' => 'author.view/',
                'publisher.view' => 'mike.publisher.view/',
                'author.add' => 'author.add/',
                'publisher.add' => 'publisher.add/',
                'author.update' => 'author.update/#ID#/',
                'publisher.update' => 'publisher.update/#ID#/',
            ],
            'view'
        );

        $this->getRoute()->run();

        if($this->getRoute()->getAction() == 'view')
        {
            $grid = new GridHelper($this->gridId);
            $this->setGrid($grid);
            $grid->setFilter([
                [
                    'id' => 'TITLE',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_TITLE'),
                    'type' => 'string',
                    'default' => true,
                ],
                [
                    'id' => 'PUBLISHER',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_PUBLISHER'),
                    'type' => 'string',
                    'default' => true,
                ],
                [
                    'id' => 'AUTHOR',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_AUTHOR'),
                    'type' => 'string',
                    'default' => true,
                ],
            ]);
            $grid->setColumns([
                [
                    'id' => 'ID', // Идентификатор
                    'name' => 'ID', // Отображаемое название
                    'sort' => 'ID', // По какому столбцу в БД сортировать (false - если сортировка запрещена)
                    'default' => true, // Отображать по умолчанию
                ],
                [
                    'id' => 'TITLE',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_TITLE'),
                    'sort' => 'TITLE',
                    'default' => true,
                ],
                [
                    'id' => 'PUBLISHER',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_PUBLISHER'),
                    'sort' => 'PUBLISHER.NAMECOMPANY',
                    'default' => true,
                ],
                [
                    'id' => 'AUTHOR',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_AUTHOR'),
                    'sort' => 'AUTHOR.NAME',
                    'default' => true,
                ],
                [
                    'id' => 'CREATEAT',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_CREATEAT'),
                    'sort' => 'CREATEAT',
                    'default' => true,
                ],
                [
                    'id' => 'UPDATEAT',
                    'name' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_GRID_COLUMN_UPDATEAT'),
                    'sort' => 'UPDATEAT',
                    'default' => true,
                ]
            ]);

            $filter = $grid->getFilterData();

            $result = BookTable::getList(
                [
                    'limit' => $grid->getNavigation()->getLimit(), // Ограничение выборки
                    'offset' => $grid->getNavigation()->getOffset(), // Смещение
                    'order' => $grid->getSort(), // Сортировка SORT
                    'filter' => $filter, // Фильтр WHERE
                    'count_total' => true, // Нужно чтобы не получать отдельным запросом количество записей по фильтру
                    'select'=> ['*', 'PUBLISHER.NAMECOMPANY', 'AUTHOR.NAME'], // Какие поля выбирать SELECT
                ]
            );

            while($item = $result->fetchObject()) {
                $grid->addRow(
                    [
                        'data' => [
                            'ID' => $item->getId(),
                            'PUBLISHER' => $item->getPublisher()==nulL? Loc::getMessage('ITB_MIKE_BOOK_VIEW_PUBLISHER_DEFAULT'):$item->getPublisher()->getNamecompany(),
                            'AUTHOR' =>  $item->getAuthor()==null? Loc::getMessage('ITB_MIKE_BOOK_VIEW_AUTHOR_DEFAULT'):$item->getAuthor()->getName(),
                            'TITLE' => $item->getTitle(),
                            'CREATEAT' => $item->getCreateat(),
                            'UPDATEAT' => $item->getUpdateat(),

                        ],
                        'actions' => [
                            [
                                'text' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_ACTION_READ'),
                                'default' => true,
                                'onclick' => 'BX.ready(function(){
                                BX.SidePanel.Instance.open(
                                    "' . $this->getRoute()->getUrl('book.update', ['ID' => $item->getId()]) . '",
                                    {
                                        cacheable: false,
                                        width: 800
                                    }
                                );
                            })',
                            ],

                            [
                                'text' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_ACTION_DELETE'),
                                'default' => false,
                                'onclick' => (new JsCode('deleteBook('.$item->getId().', "' . $this->grid->getGridId() . '")'))->getCode(),
                            ]
                        ]
                    ]
                );
            }

            $grid->getNavigation()->setRecordCount($result->getCount()); // Устанавливаем количество выбранных записей
        }
        $this->includeComponentTemplate();
    }
}