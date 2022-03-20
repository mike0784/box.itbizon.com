<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Itbizon\Mike\BookTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\RouterHelper;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class BookUpdate extends CBitrixComponent
{
    protected $mass = array();
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
            $data = $this->_request->getPostList();
            if(!is_null($data))
            {
                BookTable::update($data['ID'], array('ID_PUBLISHER' => $data['ID_PUBLISHER'], 'ID_AUTHOR' => $data['ID_AUTHOR'], 'TITLE' => $data['TITLE']));
            }
        }

        $this->includeComponentTemplate();
    }

    public function getResult()
    {
        return $this->mass;
    }
}