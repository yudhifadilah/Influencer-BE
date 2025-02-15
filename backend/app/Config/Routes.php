<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('api/login', 'Auth::login');
$routes->post('api/register', 'Auth::register');
$routes->get('api/profile', 'Auth::profile');
$routes->post('api/logout', 'Auth::logout');

$routes->group('api', function ($routes) {
    $routes->post('influencers/register', 'InfluencerController::register');
    $routes->get('influencers', 'InfluencerController::index');
    $routes->get('campaigns/influencer/(:num)', 'CampaignController::getByInfluencer/$1');
    $routes->get('influencers/(:num)', 'InfluencerController::show/$1');
    $routes->put('influencers/(:num)', 'InfluencerController::update/$1');
    $routes->delete('influencers/(:num)', 'InfluencerController::delete/$1');
});

$routes->group('api/brands', function ($routes) {
    $routes->post('register', 'BrandController::register'); 
    $routes->get('/', 'BrandController::index');           
    $routes->get('(:num)', 'BrandController::show/$1');   
    $routes->put('(:num)', 'BrandController::update/$1'); 
    $routes->delete('(:num)', 'BrandController::delete/$1'); 
});


$routes->group('api/campaigns', function ($routes) {
    $routes->post('create', 'CampaignController::create');
    $routes->get('/', 'CampaignController::index');
    $routes->get('(:num)', 'CampaignController::show/$1');
    $routes->post('update-status/(:num)', 'CampaignController::updateStatus/$1');
    $routes->delete('(:num)', 'CampaignController::delete/$1');
});
