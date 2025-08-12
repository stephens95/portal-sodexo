<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------- Authentication Routes ---------
// $routes->get('/', function () {
//     return view('auth/login');
// });

$routes->get('/', 'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Halaman setelah login
$routes->get('/home', function () {
    if (!session()->get('logged_in')) {
        return redirect()->to('/');
    }
    return view('home');
    // echo "Welcome, " . session()->get('name') . " | <a href='/logout'>Logout</a>";
});

$routes->get('/news-updates', 'ProgramUpdates::index');

// --------- Account Settings Routes ---------
$routes->get('/account-settings', 'Account::index');
$routes->post('/account/update', 'Account::update');

// --------- User Management Routes ---------
$routes->get('/users', 'User::index');
// $routes->get('/users/create', 'User::create');
$routes->get('/users/getUserById/(:num)', 'User::getUserById/$1');
$routes->post('/users/updateUser', 'User::updateUser');
$routes->post('users/createUser', 'User::createUser');
$routes->get('roles/listAll', 'Role::listAll');
$routes->get('buyers/listAll', 'Buyer::listAll');
$routes->get('/users/delete/(:num)', 'User::delete/$1');

// $routes->post('/users/store', 'User::store'); // Uncomment if you have a store method

// -------- Report Inventory ----------------
$routes->get('/report-inventory', 'Inventory::index');

// --------- Dashboard Routes ---------
$routes->get('/home', function () {
    return view('home');
});

// ---------- API Routes ---------
// $routes->get('api/api_sodexo/(:num)', 'LineItemAPI::getLineItems/$1');
$routes->group('portal', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('api-sodexo/inventory/(:num)', 'LineItemAPI::getLineItems/$1'); // GET /api/users/1
});
