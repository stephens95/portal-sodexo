<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------- Auth Routes ---------
$routes->get('/', 'AuthController::index');
$routes->get('/register', 'AuthController::register');
$routes->get('/forgot-password', 'AuthController::forgotPassword');
$routes->post('/login', 'AuthController::login');
$routes->post('/register', 'AuthController::processRegister');
$routes->post('/forgot-password', 'AuthController::processForgotPassword');
$routes->get('/logout', 'AuthController::logout');

// --------- Protected Routes ---------
$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('/home', 'HomeController::index');
    $routes->get('/news-updates', 'ProgramUpdateController::index');

    // Buyers page & API
    $routes->get('/buyers', 'BuyerController::index');
    $routes->get('/buyers/listAll', 'BuyerController::listAll');
    $routes->match(['get', 'post'], '/buyers/refresh', 'BuyerController::refreshFromSap');

    // Account Settings Routes
    $routes->get('/account-settings', 'AccountController::index');
    $routes->post('/account/update', 'AccountController::update');

    // User Management Routes
    $routes->get('/users', 'UserController::index');
    $routes->get('/users/getUserById/(:num)', 'UserController::getUserById/$1');
    $routes->post('/users/update', 'UserController::update');
    $routes->post('/users/create', 'UserController::create');
    $routes->post('/users/toggle-verification', 'UserController::toggleVerification');
    $routes->get('/roles/listAll', 'Role::listAll');;
    $routes->get('/users/delete/(:num)', 'UserController::delete/$1');

    // Report Inventory
    $routes->get('/report-inventory', 'InventoryController::index');
    $routes->post('/report-inventory/data', 'InventoryController::getInventoryData');
    $routes->post('/report-inventory/refresh-cache', 'InventoryController::refreshCache');
    $routes->get('/report-inventory/export-excel', 'InventoryController::exportExcel');
    $routes->get('/report-inventory/export-csv', 'InventoryController::exportCsv');

    // Report Sales Order Traceability
    $routes->get('/report-so', 'SalesOrderController::index');
    $routes->post('/report-so/data', 'SalesOrderController::getSalesOrderData');
    $routes->post('/report-so/refresh-cache', 'SalesOrderController::refreshCache');
    $routes->get('/report-so/export-excel', 'SalesOrderController::exportExcel');
    $routes->get('/report-so/export-csv', 'SalesOrderController::exportCsv');

    // Sales Order Routes
    // $routes->get('/sales-order', 'SalesOrderController::index');

    // Documentation API
    // $routes->get('/api-inventory', 'Api\DocumentationController::inventory');
});
$routes->get('/api-inventory', 'API\ApiController::getInventory');

// $routes->group('', ['filter' => 'auth', 'namespace' => 'App\Controllers\Api'], function ($routes) {
//     $routes->get('api-inventory', 'ApiController::getInventory');
// });

// API Routes
// $routes->group('portal', ['namespace' => 'App\Controllers\Api'], function ($routes) {
//     $routes->get('api-sodexo/inventory/(:num)', 'LineItemAPI::getLineItems/$1');
// });
