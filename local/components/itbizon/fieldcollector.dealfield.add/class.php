<?php

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Uri;
use Itbizon\Service\Component\Simple;

use Bizon\Main\FieldCollector\Model\DealFieldTable;
use Bizon\Main\FieldCollector\DealField;


if(!Loader::includeModule('itbizon.service')) {
    throw new Exception('Ошибка подключения модуля itbizon.service');
}

if (!Loader::includeModule('crm'))
    throw new Exception('Ошибка подключения модуля crm');


/**
 * Class CITBFieldcollectorDealfieldAdd
 */
class CITBFieldcollectorDealfieldAdd extends Simple
{

    public array $catList;
    public array $fieldTypeList;


    /**
     * @return mixed|void|null
     */
    public function executeComponent()
    {
        try {
            // Берем роуты из параметров (их должен передать родительский роутер)
            $this->setRoute($this->arParams['HELPER']);

            $this->catList = DealFieldTable::getCategoryList();

            $this->fieldTypeList = [];
            $dealFieldList = DealFieldTable::getDealFields();
            $ufList = DealFieldTable::getDealUserFields();
            $this->fieldTypeList = array_merge($this->fieldTypeList, $dealFieldList);
            $this->fieldTypeList = array_merge($this->fieldTypeList, $ufList);

            // Объект запроса
            $request = Application::getInstance()->getContext()->getRequest();
            if ($request->getPost('save') === 'Y') { // Проверяем что отправлена форма
                // Массив полей из запроса
                $data = $request->getPost('DATA');

                // Добавляем запись
                $result = DealFieldTable::add($data);
                if (!$result->isSuccess()) {
                    throw new Exception(implode('; ', $result->getErrorMessages()));
                }

                // Закрываем сайдслайдер (делаем гет запрос компонента самого на себя)
                $uri = (new Uri($this->getRoute()->getUrl('add')));
                if($this->getRoute()->isInSliderMode()) {
                    $uri->addParams(['IFRAME' => 'Y']);
                }
                LocalRedirect($uri->getLocator());
                die();
            }
        } catch (Exception $e) {
            $this->addError($e->getMessage()); // Фиксируем ошибку в коллекции ошибок компонента
        }
        // Подключаем шаблон компонента
        $this->IncludeComponentTemplate();
    }
}