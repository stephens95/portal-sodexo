<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
// $routes->get('api/lineitems/(:num)', 'LineItemAPI::getLineItems/$1');

$routes->group('api', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('lineitems/(:num)', 'LineItemAPI::getLineItems/$1'); // GET /api/users/1
});
