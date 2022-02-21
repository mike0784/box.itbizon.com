<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Routing\RoutingConfigurator;
use Itbizon\API\Controller\Base;
use Itbizon\API\Controller\Test;

return function (RoutingConfigurator $routes) {
    if (Loader::includeModule('itbizon.api')) {
        $routes->prefix('api')->name('api')->group(function (RoutingConfigurator $routes) {
            $routes->prefix('v1')->name('v1')->group(function (RoutingConfigurator $routes) {

                // api/v1/test
                $routes->any('test',  [Test::class, 'index'])->methods(['GET', 'POST']);

                //Default route - not found
                $routes->any('{request}', [Base::class, 'notfound'])->where('request', '.*');
            });
            //Default route - not found
            $routes->any('{request}', [Base::class, 'notfound'])->where('request', '.*');
        });
    }
};