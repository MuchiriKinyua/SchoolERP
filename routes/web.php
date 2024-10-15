<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;

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

Route::get('/mpesa/pay', [MpesaController::class, 'pay'])->name('mpesa.pay');

Route::get('/api/mpesa/callback', [MpesaController::class, 'callback'])->name('mpesa.callback');

Route::controller(PaymentController::class)
    ->prefix('payments')
    ->as('payments.')
    ->group(function () {
        Route::post('/initiatepush', 'initiateStkPush')->name('initiatepush');
        Route::get('/token', 'token')->name('token');
        Route::post('/stkCallback', 'stkCallback')->name('stkCallback');
        Route::get('/stkquery', 'stkQuery')->name('stkquery');
        Route::get('/registerurl', 'registerurl')->name('registerurl');
        Route::post('/validation', 'Validation')->name('validation');
        Route::post('/confirmation', 'Confirmation')->name('confirmation');
        Route::get('/simulate', 'Simulate')->name('simulate');
        Route::get('/qrcode', 'qrcode')->name('qrcode');
    });

Route::post('/payments/stkCallback', [PaymentController::class, 'stkCallback']);

Route::post('/check-email', [UserController::class, 'checkUserEmail']);

Route::post('/payments', [PaymentController::class, 'callback']);

Route::get('/initiatepush', [PaymentController::class, 'initiateStkPush']);

Route::get('/accounts', function () {return view('accounts');});

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