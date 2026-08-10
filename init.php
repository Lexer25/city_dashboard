<?php
// MODPATH/about/init.php
defined('SUMMARY_VERSION') OR define('SUMMARY_VERSION', '2.0.0');

	
	
Kohana::$config->load('menu')
    ->set('dashboard', array(
        'title' => 'dashboard',
        'url' => 'dashboard',
        'icon' => 'fa-cog',
        'order' => 2,
		'disabled' => true, 
        
    ));