<?php

use App\Http\Controllers\Web\DocsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('docs')->name('docs.')->group(function() {
    Route::middleware(['auth:web', 'guest:web'])
        ->get('/', fn () => null)
        ->name('index');

    Route::middleware('guest:web')
        ->get('login', [DocsController::class, 'login'])
        ->name('login');
    Route::post('login', [DocsController::class, 'auth'])
        ->name('login.post');

    Route::get('logout', [DocsController::class, 'logout'])
        ->name('logout');
});
