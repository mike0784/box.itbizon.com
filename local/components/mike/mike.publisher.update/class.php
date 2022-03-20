<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Itbizon\Mike\PublisherTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\RouterHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class PublisherUpdate extends Simple
{
    protected $mass = array();
    public $id;
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

        $this->id = intval($this->getRoute()->getVariable('IDPUBLISHER'));

        $this->_request = Application::getInstance()->getContext()->getRequest();
        if($this->_request->getPost('update'))
        {
            $data = $this->_request->getPostList();
            if(!is_null($data))
            {
                PublisherTable::update($this->id, array('NAMECOMPANY' => $data['PUBLISHER'], 'UPDATEAT' => new DateTime));
            }
        }

        $this->includeComponentTemplate();
    }

    public function getResult()
    {
        return $this->mass;
    }
}