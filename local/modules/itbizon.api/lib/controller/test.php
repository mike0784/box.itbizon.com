<?php

namespace Itbizon\API\Controller;


class Test extends Base
{
    public function indexAction()
    {
        return [
            'test' => 'ok'
        ];
    }
}