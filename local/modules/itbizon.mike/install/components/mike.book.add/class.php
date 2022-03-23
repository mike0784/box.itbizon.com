<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;
use Itbizon\Mike\BookTable;
use Itbizon\Mike\PublisherTable;
use Itbizon\Mike\AuthorTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\RouterHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookAdd extends Simple
{
    protected $mass = array();
    public $listPublisher;
    public $listAuthor;
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
        $this -> _checkModules();

        if (!isset($this->arParams['HELPER']) || !is_a($this->arParams['HELPER'], RouterHelper::class)) {
            throw new Exception("Некорректный вход");
        }

        $this->setRoute($this->arParams['HELPER']);

        $this->createListAuthor();
        $this->createListPublisher();

        $this->_request = Application::getInstance()->getContext()->getRequest();
        if($this->_request->getPost('save') === 'Y')
        {
            $data = $this->_request->getPost('DATA');
            if(!is_null($data))
            {
                $result = BookTable::add(array('IDPUBLISHER' => $data['IDPUBLISHER'], 'IDAUTHOR' => $data['IDAUTHOR'], 'TITLE' => $data['TITLE']));
                if(!$result->isSuccess()){
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }
            }

            //Закрытие слайдера
            $uri = (new Uri($this->getRoute()->getUrl('book.add')));
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
            'select' => ['ID', 'NAMECOMPANY']
        ]);

        while ($item = $result->fetch()) {
            $this->listPublisher[$item["ID"]] = $item['NAMECOMPANY'];
        }
    }

    public function createListAuthor()
    {
        $result = AuthorTable::getList([
            'select' => ['ID', 'NAME']
        ]);

        while ($item = $result->fetch()) {
            $this->listAuthor[$item["ID"]] = $item['NAME'];
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
}