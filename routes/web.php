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

// Route::get('/mpesa/pay', [MpesaController::class, 'pay'])->name('mpesa.pay');
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
        Route::post('/b2b', 'b2b')->name('b2b');
        Route::get('/b2bresult', 'b2bResult')->name('b2bresult');
        Route::get('/b2btimeout', 'b2bTimeout')->name('b2btimeout');
    });

Route::get('/generate-qr-code', [PaymentController::class, 'generateQRCode'])->name('generate.qr.code');
Route::get('/accounts', function () {
    return redirect('/generate-qr-code');
});
Route::post('/check-email', [UserController::class, 'checkUserEmail']);
Route::post('/payments', [PaymentController::class, 'callback']);

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
