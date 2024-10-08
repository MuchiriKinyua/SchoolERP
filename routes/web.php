<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MPESAC2BController;
use App\Http\Controllers\MpesaSTKPUSHController;

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

Auth::routes([
    'verify' => true
]);

Route::post('/v1/mpesatest/stk/push', [MpesaSTKPUSHController::class, 'STKPush']);

Route::post('register-urls', [MPESAC2BController::class, 'registerURLS']);

Route::post('validation', [MPESAC2BController::class, 'validation'])->name('c2b.validate');

Route::post('confirmation', [MPESAC2BController::class, 'confirmation'])->name('c2b.confirm');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('verified');

Route::resource('admin/employees', App\Http\Controllers\Admin\employeesController::class)
    ->names([
        'index' => 'admin.employees.index',
        'store' => 'admin.employees.store',
        'show' => 'admin.employees.show',
        'update' => 'admin.employees.update',
        'destroy' => 'admin.employees.destroy',
        'create' => 'admin.employees.create',
        'edit' => 'admin.employees.edit'
    ]);