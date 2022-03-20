<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\AuthorTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\GridHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class AuthorView extends Simple
{
    protected $result = array();
    public $gridId = 'mike_author';
    public $gridRows = array();
    public $gridColumns = [
        ['id' => 'IDAUTHOR', 'name' => 'ID', 'sort' => 'ID', 'default' => true],
        ['id' => 'AUTHOR', 'name' => 'Имя автора', 'sort' => 'AUTHOR', 'default' => true],
        ['id' => 'CREATEAT', 'name' => 'Дата создания', 'sort' => 'CREATEAT', 'default' => true],
        ['id' => 'UPDATEAT', 'name' => 'Дата обнавления', 'sort' => 'UPDATEAT', 'default' => true],
    ];

    public $gridNav;
    public $listAuthor = array();
    public $listPublisher = array();
    protected  $grid;
    /**
     * Проверка подключения модуля
     */
    public function _checkModules()
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('ITB_MIKE_AUTHOR_VIEW_ERROR_MODULE'));
        }
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent()
    {
        $this->_checkModules();

        $this->setRoute($this->arParams['HELPER']);

        $this->grid = new GridHelper($this->gridId);
        $this->grid->setColumns($this->gridColumns);
        $this->gridNav = $this->grid->getNavigation();

        $this->getSelect();

        $this->gridRows = $this->grid->getRows();

        $this->includeComponentTemplate();
    }

    public function getSelect()
    {
        $query = new Bitrix\Main\Entity\Query(AuthorTable::getEntity());
        $query -> setSelect(array('*'));
        $query->setFilter(array($this->grid->getFilterData()));
        $q = $query->exec();

        while($item = $q->fetchObject()) {
            $this->grid->addRow(
                [
                    'data' => [
                        'IDAUTHOR' => $item->getIdauthor(),
                        'AUTHOR' => $item->getName()==nulL? Loc::getMessage('ITB_MIKE_AUTHOR_VIEW_NONAME'):$item->getName(),
                        'CREATEAT' => $item->getCreateat(),
                        'UPDATEAT' => $item->getUpdateat(),

                    ],
                    'actions' => [
                        [
                            'text' => Loc::getMessage('ITB_MIKE_AUTHOR_VIEW_ACTION_READ'),
                            'default' => true,
                            'onclick' => 'BX.ready(function(){
                                BX.SidePanel.Instance.open(
                                    "' . $this->getRoute()->getUrl('mike.author.update', ['IDAUTHOR' => $item->getIdauthor()]) . '",
                                    {
                                        cacheable: false,
                                        width: 600
                                    }
                                );
                            })',
                        ],

                        [
                            'text' => Loc::getMessage('ITB_MIKE_AUTHOR_VIEW_ACTION_DELETE'),
                            'default' => false,
                            'onclick' => (new JsCode('deleteAuthor('.$item->getIdauthor().', "' . $this->grid->getGridId() . '")'))->getCode(),
                        ]
                    ]
                ]
            );
        }
    }

    public function getResult()
    {
        return $this->result;
    }
}