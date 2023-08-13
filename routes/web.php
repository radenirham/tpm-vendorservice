<?php

use App\Http\Controllers\Sample\SampleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/server', '\App\Http\Controllers\ServerController@run')->name('run');

/* Route::get('/test', '\App\Http\Controllers\Sample\SampleController@index');


if(isset($_SERVER['REQUEST_URI'])) {
    $uri = str_replace('/index.php/', '', explode('?', $_SERVER['REQUEST_URI'], 2)[0]);
    if (substr($uri, 0, 1) == '/') {
        $uri = ltrim($uri, '/');
    }
    $realUri = $uri;


    Route::get('/' . $realUri, callbackWeb($realUri));
    Route::post('/' . $realUri, callbackWeb($realUri));
    Route::put('/' . $realUri, callbackWeb($realUri));
    Route::patch('/' . $realUri, callbackWeb($realUri));
    Route::delete('/' . $realUri, callbackWeb($realUri));
    Route::options('/' . $realUri, callbackWeb($realUri));
}

function callbackWeb($uri)
{
    if ($uri != '') {
        $segment = explode('/', $uri);
        if (count($segment) == 3) {
            return '\\App\\Http\\Controllers\\' . ucfirst($segment[0]) . 'Controller@' . ($segment[1] == '' ? 'index' : $segment[1]);
        } else if (count($segment) == 4) {
            return '\\App\\Http\\Controllers\\' . ucfirst($segment[0]) . "\\" . ucfirst($segment[1]) . 'Controller@' . ($segment[2] == '' ? 'index' : $segment[2]);
        } else {
            return '\\App\\Http\\Controllers\\' . ucfirst($segment[0]) . 'Controller@index';
        }
    }
} */