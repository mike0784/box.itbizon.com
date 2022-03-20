<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Itbizon\Mike\BookTable;
use ItBizon\Mike\BookAdd;
use Itbizon\Mike\PublisherTable;
use Itbizon\Mike\AuthorTable;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\GridHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookView extends Complex
{
    protected $result = array();
    public $gridId = 'mike_book';
    public $gridRows = array();
    public $gridColumns = [
        ['id' => 'ID_BOOK', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'PUBLISHER', 'name' => 'Наименование организации', 'sort' => 'DATE', 'default' => true],
        ['id' => 'AUTHOR', 'name' => 'Автор', 'sort' => 'DATE', 'default' => true],
        ['id' => 'TITLE', 'name' => 'Наименование книги', 'sort' => 'TITLE', 'default' => true],
        ['id' => 'CREATE_AT', 'name' => 'Дата создания', 'sort' => 'CREATE_AT', 'default' => true],
        ['id' => 'UPDATE_AT', 'name' => 'Дата обнавления', 'sort' => 'UPDATE_AT', 'default' => true],
    ];
    public $gridNav;
    public $listAuthor = array();
    public $listPublisher = array();
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

        $grid = new GridHelper($this->gridId);
        $grid->setColumns($this->gridColumns);
        $this->gridNav = $grid->getNavigation();

        $result = BookTable::getList([
            'select' => ['*', 'PUBLISHER', 'AUTHOR', 'TITLE', 'CREATE_AT', 'UPDATE_AT'],
            'filter' => $grid->getFilterData(),
            'limit' => $grid->getNavigation()->getLimit(),
            'offset' => $grid->getNavigation()->getOffset(),
            'order' => $grid->getSort(['sort' => ['ID_BOOK' => 'DESC']]),
            'count_total' => true,
        ]);


        while($item = $result->fetchObject()) {
            $grid->addRow(
                [
                    'data' => [
                        'ID_BOOK' => $item->getId_book(),
                        'PUBLISHER' => $item->getPublisher()==nulL? Loc::getMessage('ITB_MIKE_BOOK_VIEW_PUBLISHER_DEFAULT'):$item->getPublisher()->getName_company(),
                        'AUTHOR' =>  $item->getAuthor()==null? Loc::getMessage('ITB_MIKE_BOOK_VIEW_AUTHOR_DEFAULT'):$item->getAuthor()->getName(),
                        'TITLE' => $item->getTitle(),
                        'CREATE_AT' => $item->getCreate_at(),
                        'UPDATE_AT' => $item->getUpdate_at(),

                    ],
                    'actions' => [
                        [
                            'text' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_ACTION_READ'),
                            'default' => true,
                        ],

                        [
                            'text' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_ACTION_DELETE'),
                            'default' => false,
                        ]
                    ]
                ]
            );
        }
        $this->createListPublisher();
        $this->createListAuthor();
        $this->gridRows = $grid->getRows();
        $this->includeComponentTemplate();
    }

    public function createListPublisher(): void
    {
        $result = PublisherTable::getList([
            'select' => ['*'],

            'count_total' => true,
        ]);

        while($item = $result->fetch())
        {
            $this->listPublisher[] = $item;
        }
    }

    public function createListAuthor()
    {
        $result = AuthorTable::getList([
            'select' => ['*'],
            //'order' => ['sort' => ['ID_AUTHOR' => 'DESC']],
            'count_total' => true,
        ]);

        while($item = $result->fetch())
        {
            $this->listAuthor[] = $item;
        }
    }

    public function getSelect()
    {
        $query = BookTable::getList(array('select' => array('*', 'PUBLISHER.NAME_COMPANY', 'AUTHOR.NAME', 'TITLE', 'CREATE_AT', 'UPDATE_AT')));

        while ($row = $query->fetch()) {
            $this->result[] = $row;
        }
    }

    public function getResult()
    {
        return $this->result;
    }
}