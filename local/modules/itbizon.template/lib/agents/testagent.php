<?php

namespace Itbizon\Template\Agents;

class TestAgent
{
    /**
     * @return string
     */
    public static function testAgent()
    {
        mail('panarin.a@itbizon.com', 'Агент', 'Агент');
        return __METHOD__ . "();";
    }
}