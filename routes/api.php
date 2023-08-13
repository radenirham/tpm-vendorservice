<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */

if(isset($_SERVER['REQUEST_URI'])) {
    $uri = str_replace('/index.php/', '', explode('?', $_SERVER['REQUEST_URI'], 2)[0]);
    if (substr($uri, 0, 1) == '/') {
        $uri = ltrim($uri, '/');
    }

    Route::get('/' . $uri, callbackApi($uri));
    Route::post('/' . $uri, callbackApi($uri));
    Route::put('/' . $uri, callbackApi($uri));
    Route::patch('/' . $uri, callbackApi($uri));
    Route::delete('/' . $uri, callbackApi($uri));
    Route::options('/' . $uri, callbackApi($uri));
}

function callbackApi($uri)
{
    if ($uri != '') {
        $segment = explode('/', $uri);
        if (count($segment) == 4) {
            return '\\App\\Http\\Controllers\\' . ucfirst($segment[0]) . '\\' . ucfirst($segment[1]) . 'Controller@' . ($segment[2] == '' ? 'index' : $segment[2]);
        } else if (count($segment) == 5) {
            return '\\App\\Http\\Controllers\\' . ucfirst($segment[0]) . '\\' . ucfirst($segment[1]) . "\\" . ucfirst($segment[2]) . 'Controller@' . ($segment[3] == '' ? 'index' : $segment[3]);
        } else {
            return '\\App\\Http\\Controllers\\' . ucfirst($segment[0]) . '\\' . ucfirst($segment[1]) . 'Controller@index';
        }
    }
}