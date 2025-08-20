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
$routes->group('', ['filter' => 'auth'], function($routes) {
    $routes->get('/home', 'HomeController::index');
    $routes->get('/news-updates', 'ProgramUpdateController::index');

    // Account Settings Routes
    $routes->get('/account-settings', 'AccountController::index');
    $routes->post('/account/update', 'AccountController::update');

    // User Management Routes
    $routes->get('/users', 'UserController::index');
    $routes->get('/users/getUserById/(:num)', 'UserController::getUserById/$1');
    $routes->post('/users/update', 'UserController::update');
    $routes->post('/users/create', 'UserController::create');
    $routes->post('/users/toggle-verification', 'UserController::toggleVerification');
    $routes->get('/roles/listAll', 'Role::listAll');
    $routes->get('/buyers/listAll', 'Buyer::listAll');
    $routes->get('/users/delete/(:num)', 'UserController::delete/$1');
    
    // Report Inventory
    $routes->get('/report-inventory', 'InventoryController::index');
    $routes->post('/report-inventory/data', 'InventoryController::getInventoryData');
    $routes->post('/report-inventory/refresh-cache', 'InventoryController::refreshCache');
    $routes->post('/report-inventory/all-data', 'InventoryController::getAllInventoryData');
    $routes->get('/report-inventory/export-excel', 'InventoryController::exportExcel');
    $routes->get('/report-inventory/export-csv', 'InventoryController::exportCsv');
});

// API Routes
$routes->group('portal', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('api-sodexo/inventory/(:num)', 'LineItemAPI::getLineItems/$1');
});
