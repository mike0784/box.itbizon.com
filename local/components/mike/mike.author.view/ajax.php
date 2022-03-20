<?php
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\AuthorTable;

class MikeAuthorViewAjaxController extends Controller
{
    /**
     * @param string $id
     * @return bool
     */

    public function deleteAuthorAction($id = 'id')
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('Модуль itbizon.mike не подключен'));
        }


        $result = AuthorTable::delete($id);
        if(!$result->isSuccess()) {
            throw new Exception('Ошибка удаления: '.implode(';', $result->getErrorMessages()));
        }
    }
}