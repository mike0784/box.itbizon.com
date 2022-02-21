<?php

namespace Itbizon\API\ActionFilter;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Engine\ActionFilter\Base;
use Bitrix\Main\Error;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;

class Auth extends Base
{
    protected string $headerName;

    public function __construct(string $headerName = 'X-AUTH-TOKEN')
    {
        $this->headerName = $headerName;
        parent::__construct();
    }

    public function onBeforeAction(Event $event)
    {
        $request = Application::getInstance()->getContext()->getRequest();
        $validToken = 'aaaa'; //TODO get valid token
        if ($request->getHeader($this->headerName) !== $validToken) {
            Context::getCurrent()->getResponse()->setStatus(401);
            $this->addError(new Error(
                'Неверный токен',
                'ERROR_AUTH'
            ));
            return new EventResult(EventResult::ERROR, null, null, $this);
        }
        return null;
    }
}