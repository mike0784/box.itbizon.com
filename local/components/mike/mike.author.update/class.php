<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\Web\Uri;
use Itbizon\Mike\AuthorTable;
use Itbizon\Service\Component\Simple;
use Itbizon\Service\Component\RouterHelper;


if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class AuthorUpdate extends Simple
{
    protected $mass = array();
    public $id;
    public $name;
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

        $this->id = intval($this->getRoute()->getVariable('IDAUTHOR'));
        $author = AuthorTable::getByPrimary($this->id)->fetchObject();
        if(!$author) {
            throw new Exception(Loc::getMessage('Данного автора нет'));
        }
        $this->name = $author->getName();

        $request = Application::getInstance()->getContext()->getRequest();
        if($request->getPost('save') === 'Y')
        {
            $data = $request->getPost('DATA');
            if(!is_null($data))
            {
                $result = AuthorTable::update($this->id, array('NAME' => $data['NAME'], 'UPDATEAT' => new DateTime));
                if(!$result->isSuccess()){
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }
            }
            $uri = (new Uri($this->getRoute()->getUrl('author.update', ['IDAUTHOR' => $this->id])));
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
        return $this->mass;
    }
}