<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Itbizon\Mike\AuthorTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class AuthorAdd extends CBitrixComponent
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
       $this -> _checkModules();

        $this->_request = Application::getInstance()->getContext()->getRequest();
        if($this->_request->getPost('add'))
        {
            $data = $this->_request->getPost('AUTHOR');
            if(!is_null($data))
            {
                AuthorTable::add(array('NAME' => $data));
            }
        }

        $this->includeComponentTemplate();
    }

    public function getResult()
    {
        return $this->result;
    }
}