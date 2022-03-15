<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Itbizon\Mike\BookTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookView extends CBitrixComponent
{
    protected $result = array();
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

        $this->_request = Application::getInstance()->getContext()->getRequest();
        if($this->_request->getPost('update'))
        {
            $this->getSelect();
        }
        else $this->getSelect();

        $this->includeComponentTemplate();
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