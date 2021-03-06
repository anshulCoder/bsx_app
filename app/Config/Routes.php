<?php namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/admin', 'Admin::index');
$routes->get('/user', 'User::index');
$routes->get('/user/new-accuracy-bet', 'User::add_accuracy_bet');
$routes->get('/user/new-battle-bet', 'User::add_battle_bet');
$routes->get('/user/new-sequel-bet', 'User::add_sequel_bet');
$routes->get('/user/new-exchange-bet', 'User::add_exchange_bet');
$routes->get('/user/login', 'User::login');
$routes->get('/user/accept-battle/(:num)', 'User::accept_battle/$1');
$routes->get('/user/deny-battle/(:num)', 'User::deny_battle/$1');
$routes->get('/user/participate-public-battle/(:num)', 'User::participate_public_battle/$1');

$routes->get('/tmdb/test-fetch-now-movies', 'TmdbController::fetch_latest_media');
/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
