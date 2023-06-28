<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Admin\UserController;
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
})->name('welcome');

Route::get('/home', function () {
    return view('home');
});


Route::get('redirects', 'App\Http\Controllers\HomeController@index');

Route::middleware(['auth', 'role:1'])->group(function () {

    // Au cas ou il y est un dashboard superadmin

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');

    // Create
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

    // Edit
    Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{id}', [UserController::class, 'update'])->name('admin.users.update');


    // Système de suppression
    Route::delete('/admin/users/destroy-multiple', [UserController::class, 'destroyMultiple'])->name('admin.users.destroy-multiple');
    Route::get('/admin/users/trash', [UserController::class, 'trash'])->name('admin.users.trash');
    Route::delete('/admin/users/force-destroy-multiple', [UserController::class, 'forceDestroyMultiple'])->name('admin.users.force-destroy-multiple');

    // Restore
    Route::post('/admin/users/restore-multiple', [UserController::class, 'restoreMultiple'])->name('admin.users.restore-multiple');

});

Route::middleware(['auth', 'role:1,2,3,4'])->group(function () {

    // Pour le dasboard admin

});

Route::group(['middleware' => 'auth'], function() {
    // Route pour tous les rôles à part user
    Route::group([
        'middleware' => 'role:1,2,3,4', 
        'prefix' => 'admin',
        'name' => 'admin.',
        'as' => 'admin.'
    ], function() {
        Route::get('/dashboard', function() {
            return view('admin.dashboard');
        })->name('dashboard');
    });
    
});


