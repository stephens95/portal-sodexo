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


// --------- Dashboard Routes ---------
$routes->get('/home', function () {
    return view('home');
});

// ---------- API Routes ---------
// $routes->get('api/api_sodexo/(:num)', 'LineItemAPI::getLineItems/$1');
$routes->group('portal', ['namespace' => 'App\Controllers\Api'], function ($routes) {
    $routes->get('api-sodexo/inventory/(:num)', 'LineItemAPI::getLineItems/$1'); // GET /api/users/1
});
