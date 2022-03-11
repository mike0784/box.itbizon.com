<?php


namespace Itbizon\Service\Component;


use Exception;

/**
 * Class Simple
 * @package Itbizon\Service\Component
 */
class Simple extends Base
{
    /**
     * @param string $paramName
     * @return RouterHelper
     * @throws Exception
     */
    public function initRouteFromParams(string $paramName): RouterHelper
    {
        if(!isset($this->arParams[$paramName]) || !is_a($this->arParams[$paramName], RouterHelper::class)) {
            throw new Exception('Invalid router object');
        }
        $this->route = $this->arParams[$paramName];
        return $this->route;
    }
}