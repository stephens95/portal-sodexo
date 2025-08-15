<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'AuthController::index');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// Halaman setelah login
$routes->get('/home', function () {
    if (!session()->get('logged_in')) {
        return redirect()->to('/');
    }
    return view('home');
});

$routes->get('/news-updates', 'ProgramUpdateController::index');

// --------- Account Settings Routes ---------
$routes->get('/account-settings', 'AccountController::index');
$routes->post('/account/update', 'AccountController::update');

// --------- User Management Routes ---------
$routes->get('/users', 'UserController::index');
$routes->get('/users/getUserById/(:num)', 'UserController::getUserById/$1');
$routes->post('/users/update', 'UserController::updateUser');  // Changed from updateUser to update
$routes->post('users/createUser', 'UserController::createUser');
$routes->get('roles/listAll', 'Role::listAll');
$routes->get('buyers/listAll', 'Buyer::listAll');
$routes->get('/users/delete/(:num)', 'UserController::delete/$1');

// -------- Report Inventory ----------------
$routes->get('/report-inventory', 'InventoryController::index');

// --------- Dashboard Routes ---------
$routes->get('/home', function () {
    return view('home');
});

// ---------- API Routes ---------
$routes->group('portal', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('api-sodexo/inventory/(:num)', 'LineItemAPI::getLineItems/$1'); // GET /api/users/1
});
