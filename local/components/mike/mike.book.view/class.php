<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\BookTable;
use ItBizon\Mike\BookAdd;
use Itbizon\Mike\PublisherTable;
use Itbizon\Mike\AuthorTable;
use Itbizon\Service\Component\Complex;
use Itbizon\Service\Component\GridHelper;
use Bitrix\Main\Entity\Query;

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
                'mike.book.add' => 'mike.book.add/',
                'mike.book.update' => 'mike.book.update/#IDBOOK#/',
                'mike.author.view' => 'mike.author.view/',
                'mike.publisher.view' => 'mike.publisher.view/',
                'mike.author.add' => 'mike.author.add/',
                'mike.publisher.add' => 'mike.publisher.add/',
                'mike.author.update' => 'mike.author.update/#IDAUTHOR#/',
                'mike.publisher.update' => 'mike.publisher.update/#IDPUBLISHER#/',
            ],
            'view'
        );

        $this->getRoute()->run();

        $this->grid = new GridHelper($this->gridId);
        $this->grid->setColumns($this->gridColumns);
        $this->gridNav = $this->grid->getNavigation();
        $this->getSelect();

        $this->includeComponentTemplate();
    }



    public function getSelect()
    {
        $q = new Bitrix\Main\Entity\Query(BookTable::getEntity());
        $q->setSelect(array('IDBOOK', 'PUBLISHER', 'AUTHOR', 'TITLE', 'CREATEAT', 'UPDATEAT'));
        $q->setFilter(array($this->grid->getFilterData()));
        $result = $q->exec();

        while($item = $result->fetchObject()) {
            $this->grid->addRow(
                [
                    'data' => [
                        'IDBOOK' => $item->getIdbook(),
                        'PUBLISHER' => $item->getPublisher()==nulL? Loc::getMessage('ITB_MIKE_BOOK_VIEW_PUBLISHER_DEFAULT'):$item->getPublisher()->getNamecompany(),
                        'AUTHOR' =>  $item->getAuthor()==null? Loc::getMessage('ITB_MIKE_BOOK_VIEW_AUTHOR_DEFAULT'):$item->getAuthor()->getName(),
                        'TITLE' => $item->getTitle(),
                        'CREATE_AT' => $item->getCreateat(),
                        'UPDATE_AT' => $item->getUpdateat(),

                    ],
                    'actions' => [
                        [
                            'text' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_ACTION_READ'),
                            'default' => true,
                            'onclick' => 'BX.ready(function(){
                                BX.SidePanel.Instance.open(
                                    "' . $this->getRoute()->getUrl('mike.book.update', ['IDBOOK' => $item->getIdbook()]) . '",
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
                            'onclick' => (new JsCode('deleteBook('.$item->getIdbook().', "' . $this->grid->getGridId() . '")'))->getCode(),
                        ]
                    ]
                ]
            );
        }
        $this->gridRows = $this->grid->getRows();
    }

    public function getResult()
    {
        return $this->result;
    }
}