<?php

use \Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

//echo 'module scratch';
echo Loc::getMessage("ITB_TEST.MODULE_NAME");