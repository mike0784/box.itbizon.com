<?php

namespace Itbizon\API\Controller;


use Bitrix\Main\Engine\Controller;

class Test extends Base
{
    public function configureActions()
    {
        return [];
    }

    public function indexAction()
    {
        $response = new \Bitrix\Main\HttpResponse();
        $response->addHeader('Content-Type', 'application/json');
        $response->setContent(json_encode(['test' => 'ok']));

        return $response;
    }
}