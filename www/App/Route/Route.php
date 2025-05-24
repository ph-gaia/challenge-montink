<?php

namespace App\Route;

use Core\RouterSystem\RouteMap;

class Route
{

    /**
     * @var RouteMap
     */
    private $routeMap;

    public function __construct(RouteMap $routeMap)
    {
        $this->routeMap = $routeMap;
    }

    /**
     * Registra as rotas no sistma
     */
    public function register()
    {
        $this->routeMap->get(array('/', 'ProductsController@index', array()));

        $this->routeMap->get(array('/products', 'ProductsController@index', array()));

        $this->routeMap->get(array('/products/create', 'ProductsController@create', array()));

        $this->routeMap->get(array('/products/edit/{id}', 'ProductsController@edit', array(
            'id' => '/\d+/'
        )));

        $this->routeMap->post(array('/products/save', 'ProductsController@save', array()));

        $this->routeMap->post(array('/cart/add/{id}', 'CartController@add', array(
            'id' => '/\d+/'
        )));

        $this->routeMap->post(array('/cart/update/{id}', 'CartController@update', array(
            'id' => '/\d+/'
        )));

        $this->routeMap->post(array('/cart/remove/{id}', 'CartController@remove', array(
            'id' => '/\d+/'
        )));

        $this->routeMap->get(array('/cart', 'CartController@index', array()));

        $this->routeMap->get(array('/cart/get', 'CartController@getCartData', array()));

        $this->routeMap->post(array('/cart/apply-coupon', 'CartController@applyCoupon', array()));

        $this->routeMap->post(array('/orders/create', 'OrdersController@create', array()));
    }

    /**
     * Retorna a referencia para o objeto RouteMap
     * @return RouteMap
     */
    public function getRouteMap()
    {
        return $this->routeMap;
    }
}
