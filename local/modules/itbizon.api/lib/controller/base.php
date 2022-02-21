<?php

namespace Itbizon\API\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Error;
use Itbizon\API\ActionFilter\Auth;

class Base extends Controller
{
    public function configureActions()
    {
        return [
            'notfound' => [
                'prefilters' => []
            ]
        ];
    }

    protected function getDefaultPreFilters()
    {
        return [
            new Auth()
        ];
    }

    public function notfoundAction()
    {
        $this->addError(new Error('No route'));
    }
}