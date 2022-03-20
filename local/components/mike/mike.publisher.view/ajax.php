<?php
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\PublisherTable;

class MikePublisherViewAjaxController extends Controller
{
    /**
     * @param string $id
     * @return bool
     */

    public function deletePublisherAction($id = 'id')
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('Модуль itbizon.mike не подключен'));
        }

        $result = PublisherTable::delete($id);
        if(!$result->isSuccess()) {
            throw new Exception('Ошибка удаления: '.implode(';', $result->getErrorMessages()));
        }
    }
}