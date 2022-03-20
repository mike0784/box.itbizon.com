<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Itbizon\Mike\BookTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Mike\PublisherTable;
use Itbizon\Mike\AuthorTable;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookUpdate extends Simple
{
    protected $mass = array();
    public $listPublisher = array();
    public $listAuthor = array();
    public $id;
    public $nameBook;
    /**
     * Проверка подключения модуля
     */
    public function _checkModules()
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('ITB_MIKE_BOOK_VIEW_ERROR_INCLUDE_MODULE'));
        }
    }

    /**
     * Точка входа в компонент
     * Должна содержать только последовательность вызовов вспомогательых ф-ий и минимум логики
     * всю логику стараемся разносить по классам и методам
     */
    public function executeComponent() {
        $this->_checkModules();

        $this->setRoute($this->arParams['HELPER']);

        $this->id = intval($this->getRoute()->getVariable('IDBOOK'));

        $this->book = BookTable::getByPrimary($this->id)->fetchObject();
        if(!$this->book) {
            throw new Exception(Loc::getMessage('ITB_MIKE_BOOK_UPDATE_ERROR_QUERY'));
        }

        $this->nameBook = $this->book->getTitle();
        $this->createListAuthor();
        $this->createListPublisher();

        $this->_request = Application::getInstance()->getContext()->getRequest();
        if($this->_request->getPost('update'))
        {
            $data = $this->_request->getPostList();
            if(!is_null($data))
            {
                BookTable::update($this->id, array('IDPUBLISHER' => $data['Publisher'], 'IDAUTHOR' => $data['Author'], 'TITLE' => $data['TITLE'], 'UPDATEAT' => new DateTime));
            }
        }

        $this->includeComponentTemplate();
    }

    public function getResult()
    {
        return $this->mass;
    }

    public function createListPublisher(): void
    {
        $result = PublisherTable::getList([
            'select' => ['IDPUBLISHER', 'NAMECOMPANY'],
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
            'select' => ['IDAUTHOR', 'NAME'],
            'count_total' => true,
        ]);

        while($item = $result->fetch())
        {
           $this->listAuthor[] = $item;
        }
    }
}