<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Web\Uri;
use Itbizon\Mike\AuthorTable;
use Itbizon\Service\Component\Simple;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class AuthorAdd extends Simple
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

        $this->setRoute($this->arParams['HELPER']);

        $request = Application::getInstance()->getContext()->getRequest();
        if($request->getPost('save'))
        {
            $data = $request->getPost('AUTHOR');
            if(!is_null($data))
            {
                $result = AuthorTable::add(array('NAME' => $data));
                if (!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }
            }

            $uri = (new Uri($this->getRoute()->getUrl('author.add')));
            if($this->getRoute()->isInSliderMode()) {
                $uri->addParams(['IFRAME' => 'Y']);
            }
            LocalRedirect($uri->getLocator());
            die();
        }

        $this->includeComponentTemplate();
    }

    public function getResult()
    {
        return $this->result;
    }
}