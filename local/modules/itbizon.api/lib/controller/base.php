<?php

namespace Itbizon\API\Controller;

use Bitrix\Main\Engine\Controller;
use Itbizon\API\ActionFilter\Auth;

abstract class Base extends Controller
{
    protected function getDefaultPreFilters()
    {
        return [
            new Auth()
        ];
    }
}