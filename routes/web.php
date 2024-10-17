<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MpesaController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

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
        Route::get('/b2c', 'b2c')->name('b2c');
        Route::get('/b2cresult', 'b2cResult')->name('b2cresult');
        Route::get('/b2ctimeout', 'b2cTimeout')->name('b2ctimeout');
    });

Route::post('/generate-qr', [PaymentController::class, 'generateQRCode']);
Route::post('/check-email', [UserController::class, 'checkUserEmail']);
Route::post('/payments', [PaymentController::class, 'callback']);

// Remove this duplicate route definition
// Route::post('/payments/initiatepush', [PaymentController::class, 'initiatePush']); // Commented out for clarity

Route::get('/accounts', function () {
    return view('accounts');
});

Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('verified');

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
