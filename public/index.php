<?php
/**
 * PHP version 7.2
 * public\index.php
 */

if (PHP_SAPI === 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

$parent_dir = dirname(__DIR__);
require $parent_dir . '/vendor/autoload.php';

// Load the environment/configuration variables
$dotenv = new \Dotenv\Dotenv($parent_dir, \TDW18\Usuarios\Utils::getEnvFileName($parent_dir));
$dotenv->load();
$dotenv->required(
    [
        'DATABASE_NAME',
        'JWT_SECRET'
    ]
);

// Instantiate the app
$settings = require $parent_dir . '/src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require $parent_dir . '/src/dependencies.php';

// Register middleware
require $parent_dir . '/src/middleware.php';

// Register routes
require $parent_dir . '/src/routes.php';

// Run app
$app->run();
