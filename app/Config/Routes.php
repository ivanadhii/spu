<?php

namespace Config;

use CodeIgniter\Router\RouteCollection;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Beranda');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Beranda::index');

$routes->get('register', 'Auth::register');
$routes->post('auth/check-username', 'Auth::checkUsername');
$routes->post('auth/check-email', 'Auth::checkEmail');
$routes->post('auth/check-email-forgot', 'Auth::checkEmailForgot');
$routes->get('login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
//$routes->get('forgot', 'Auth::forgot');
//$routes->post('forgot', 'Auth::forgot');

//$routes->get('ketentuan', 'home\Ketentuan::index');
//$routes->get('kebijakan', 'home\Kebijakan::index');

$routes->get('homepage', 'user\Homepage::index', ['filter' => 'login']);
$routes->get('createnew', 'user\Createnew::index', ['filter' => 'login']);
$routes->get('profile', 'user\Profile::index', ['filter' => 'login']);
$routes->get('myurl', 'user\MyUrl::index', ['filter' => 'login']);
$routes->post('profile/resetPassword', 'user\Profile::resetPassword', ['filter' => 'login']);

$routes->get('dashboard', 'admin\Dashboard::index', ['filter' => 'role:admin', 'ipblocker']);
$routes->get('datausers', 'admin\DataUsers::index', ['filter' => 'role:admin']);
$routes->post('admin/DataUsers/addRole', 'admin\DataUsers::addRole', ['filter' => 'role:admin']);
$routes->post('admin/DataUsers/deleteUser', 'admin\DataUsers::deleteUser', ['filter' => 'role:admin']);
$routes->get('admin/dashboard/getInactiveUsers', 'admin\Dashboard::getInactiveUsers' , ['filter' => 'role:admin']);
$routes->get('linkshistory', 'admin\LinksHistory::index', ['filter' => 'role:admin']);

// $routes->get('(:any)', 'Shortener::redirect/$1');
$routes->post('shortener/decrypt', 'Shortener::decrypt');
$routes->get('(:segment)/(:any)', 'Shortener::redirect/$1/$2');
// $routes->get('(:any)', 'Shortener::redirect/$1');

// $routes->get('(:segment)', 'Shortener::redirect/$1');
