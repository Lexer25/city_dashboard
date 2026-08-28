<?php
// MODPATH/about/init.php
defined('DASHBOARD_VERSION') OR define('DASHBOARD_VERSION', '2.0.3');

	
	
Kohana::$config->load('menu')
    ->set('dashboard', array(
        'title' => 'dashboard',
        'url' => 'dashboard',
        'icon' => 'fa-cog',
        'order' => 2,
		'disabled' => true, 
        
    ));