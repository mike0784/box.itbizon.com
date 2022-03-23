<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Uri;
use Itbizon\Mike\BookTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\RouterHelper;
use Itbizon\Mike\PublisherTable;
use Itbizon\Mike\AuthorTable;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookUpdate extends Simple
{
    public $book;
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

        if (!isset($this->arParams['HELPER']) || !is_a($this->arParams['HELPER'], RouterHelper::class)) {
            throw new Exception("Некорректный вход");
        }
        $this->setRoute($this->arParams['HELPER']);

        $this->id = intval($this->getRoute()->getVariable('ID'));

        $this->book = BookTable::getByPrimary($this->id)->fetchObject();
        if(!$this->book) {
            throw new Exception(Loc::getMessage('ITB_MIKE_BOOK_UPDATE_ERROR_QUERY'));
        }

        $this->nameBook = $this->book->getTitle();
        $this->createListAuthor();
        $this->createListPublisher();

        $this->_request = Application::getInstance()->getContext()->getRequest();
        if($this->_request->getPost('save') === 'Y')
        {
            $data = $this->_request->getPost('DATA');
            if(!is_null($data))
            {
                $result = BookTable::update($this->id, array('IDPUBLISHER' => $data['IDPUBLISHER'], 'IDAUTHOR' => $data['IDAUTHOR'], 'TITLE' => $data['TITLE'], 'UPDATEAT' => new DateTime));
                if(!$result->isSuccess()){
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }
            }

            $uri = (new Uri($this->getRoute()->getUrl('book.update', ['ID' => $this->id])));
            if($this->getRoute()->isInSliderMode()) {
                $uri->addParams(['IFRAME' => 'Y']);
            }
            LocalRedirect($uri->getLocator());
            die();
        }

        $this->includeComponentTemplate();
    }

    public function createListPublisher(): void
    {
        $result = PublisherTable::getList([
            'select' => ['ID', 'NAMECOMPANY'],
        ]);

        while($item = $result->fetch())
        {
            $this->listPublisher[$item['ID']] = $item['NAMECOMPANY'];
        }
    }

    public function createListAuthor()
    {
        $result = AuthorTable::getList([
            'select' => ['ID', 'NAME'],
        ]);

        while($item = $result->fetch())
        {
           $this->listAuthor[$item['ID']] = $item['NAME'];
        }
    }

    public function getListPublisher()
    {
        return $this->listPublisher;
    }

    public function getListAuthor()
    {
        return $this->listAuthor;
    }

    public function getValuePublisher()
    {
        return $this->book->getIdpublisher();
    }

    public function getValueAuthor()
    {
        return $this->book->getIdauthor();
    }
}