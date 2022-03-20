<?php
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\UI\Buttons\JsCode;
use Itbizon\Mike\BookTable;

class MikeBookViewAjaxController extends Controller
{
    /**
     * @param string $id
     * @return bool
     */

    public function deleteBookAction($id = 'id')
    {
        if(!Loader::includeModule('itbizon.mike')){
            throw new Exception(Loc::getMessage('Модуль itbizon.mike не подключен'));
        }

        $f = new JsCode('var pop = new BX.CDialog({
                                        "title": "Удаление книги",
                                        "content": "Удаление",
                                        "draggable": true,
                                        "resizable": true,
                                        "buttons": [BX.CDialog.btnClose,]
                                    });
                                    BX.addCustomEvent(pop, "onWindowRegister",function(){console.log(this)});
                                    pop.Show();'
        );
        $result = BookTable::delete($id);
        if(!$result->isSuccess()) {
            throw new Exception('Ошибка удаления: '.implode(';', $result->getErrorMessages()));
        }
    }
}