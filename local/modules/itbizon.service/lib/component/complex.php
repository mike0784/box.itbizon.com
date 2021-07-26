<?php


namespace Itbizon\Service\Component;


use Exception;

/**
 * Class Complex
 * @package Itbizon\Service\Component
 */
class Complex extends Base
{
    /**
     * @param array $urlTemplates
     * @param string $defaultTemplate
     * @return RouterHelper
     * @throws Exception
     */
    public function initRoute(array $urlTemplates, string $defaultTemplate): RouterHelper
    {
        $this->route = new RouterHelper($this, $urlTemplates, $defaultTemplate);
        return $this->route;
    }
}