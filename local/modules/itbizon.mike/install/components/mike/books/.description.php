<?php
/*
* Файл local/components/mikke/books/.description.php
*/
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    'NAME' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_NAME'), // название компонента
    'DESCRIPTION' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_DESCRIPTION'),
    'CACHE_PATH' => 'Y', // показывать кнопку очистки кеша
    'SORT' => 40, // порядок сортировки в визуальном редакторе
    'COMPLEX' => 'Y', // признак комплексного компонента
    'PATH' => array( // расположение компонента в визуальном редакторе
        'ID' => 'itbizon', // идентификатор верхнего уровеня в редакторе
        'NAME' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_PATH_NAME'), // название верхнего уровня в редакторе
        'CHILD' => array( // второй уровень в визуальном редакторе
            'ID' => 'mike', // идентификатор второго уровня в редакторе
            'NAME' => Loc::getMessage('ITB_MIKE_BOOK_VIEW_PATH_CHILD_NAME') // название второго уровня в редакторе
        )
    )
);