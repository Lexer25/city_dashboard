<?php
// MODPATH/dashboard/init.php
defined('DASHBOARD_VERSION') OR define('DASHBOARD_VERSION', '2.0.6');

Kohana::$config->load('menu')
    ->set('dashboard', array(
        'title'    => 'Панель управления',
        'url'      => 'dashboard',
        'icon'     => 'fa-cog',
        'order'    => 2,
        'disabled' => false,
        'children' => array(
            'tasks' => array(
                'title' => 'Панель управления',
                'url'   => 'dashboard',
            ),
            'log' => array(
                'title' => 'Лог-файлы',
                'url'   => 'dashboard/log',
            ),
        ),
    ));