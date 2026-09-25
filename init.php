<?php
// MODPATH/about/init.php
defined('DASHBOARD_VERSION') OR define('DASHBOARD_VERSION', '2.0.4');

	
	
Kohana::$config->load('menu')
    ->set('dashboard', array(
        'title' => 'dashboard',
        'url' => 'dashboard',
        'icon' => 'fa-cog',
        'order' => 2,
		'disabled' => true, 
        
    ));
	
Route::set('default_modules', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'dashboard',
		'action'     => 'index',
	));