<?php
$includeIfExists = function ($file) {
    return file_exists($file) ? include $file : false;
};
if ((!$loader = $includeIfExists(__DIR__.'/../vendor/autoload.php')) && (!$loader = $includeIfExists(__DIR__.'/../../../autoload.php'))) {
    echo 'You must set up the project dependencies using `composer install`'.PHP_EOL.
        'See https://getcomposer.org/download/ for instructions on installing Composer'.PHP_EOL;
    exit(1);
}

if(file_exists($file = __DIR__.'/../.env') || file_exists($file = __DIR__.'/../../../../.env')) {
    $dotenv = new Dotenv\Dotenv(dirname($file));
    $dotenv->load();
}
