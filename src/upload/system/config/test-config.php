<?php

define('VERSION', '3.0.2.0');

// Site
$_['site_url']         = getenv('HTTP_SERVER');
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
    'controller/*/before' => array(
        'event/language/before'
    ),
    'controller/*/after' => array(
        'event/language/after'
    ),
    'view/*/before' => array(
        500  => 'event/theme/override',
        998  => 'event/language',
        1000 => 'event/theme'
    ),
    'language/*/after' => array(
        'event/translation'
    ),
    //'view/*/before' => array(
    //	1000  => 'event/debug/before'
    //),
    'controller/*/after'  => array(
        'event/debug/after'
    )
);

if (defined('HTTP_CATALOG')) { // is defined iff in catalog

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

$_['template_engine']    = 'twig';
$_['template_directory'] = '';
$_['template_cache']     = false;
