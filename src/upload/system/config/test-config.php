<?php

define('VERSION', '2.3.0.1');
define('DIR_STORAGE', __DIR__.'/../storage/');

// Site
$_['site_base']        = getenv('HTTP_SERVER');
$_['site_ssl']         = false;

// Database
$_['db_autostart']     = true;
$_['db_type']          = DB_DRIVER; // mpdo, mssql, mysql, mysqli or postgre
$_['db_hostname']      = DB_HOSTNAME;
$_['db_username']      = DB_USERNAME;
$_['db_password']      = DB_PASSWORD;
$_['db_database']      = DB_DATABASE;
$_['db_port']          = DB_PORT;

// Autoload Libraries
$_['library_autoload'] = array(
    'openbay'
);

// Action Events
$_['action_event'] = array(
    'view/*/before' => array(
        999  => 'event/language',
        1000 => 'event/theme'
    ),
);

if (defined('HTTP_CATALOG')) { // is defined iff in catalog
    $_['config_theme'] = 'theme_default';
    $_['theme_default_status'] = 1;
    $_['action_default'] = 'common/dashboard';

    // Actions
    $_['action_pre_action'] = array(
        'startup/startup',
        'startup/error',
        'startup/event',
        'startup/sass',
        'startup/login',
        'startup/permission'
    );
} else { // admin
    // Actions
    $_['action_pre_action'] = array(
        'startup/startup',
        'startup/error',
        'startup/event',
        'startup/maintenance',
        'startup/seo_url'
    );
}

// Test Settings
$_['session_engine'] = 'test';
$_['session_autostart'] = false;

$_['theme_directory'] = 'default';