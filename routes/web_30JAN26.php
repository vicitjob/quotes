<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GateEntryDetailController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\QuotationConfigController;

Route::get('/', function () {
    //return view('welcome');
	//return view('home');
	return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');
Route::resource('gateentries', GateEntryDetailController::class)->middleware('auth');
Route::resource('quotations', QuotationController::class)->middleware('auth');
Route::resource('users', UserController::class)->middleware('auth');
Route::resource('roles', RoleController::class)->middleware('auth');
Route::resource('permissions', PermissionController::class)->middleware('auth');
Route::get('/getdata_location', [App\Http\Controllers\GateEntryDetailController::class, 'getdata_location']);
Route::get('/getpodetails', [App\Http\Controllers\GateEntryDetailController::class, 'getpodetails']);
Route::post('/savestoreuserdata', [App\Http\Controllers\GateEntryDetailController::class, 'savestoreuserdata']);
Route::post('/savecheckoutdata', [App\Http\Controllers\GateEntryDetailController::class, 'savecheckoutdata']);
Route::get('/getpdfDownload', [App\Http\Controllers\GateEntryDetailController::class, 'getpdfDownload']);
Route::get('/attachments/{id}/{fileno}/download', [AttachmentController::class, 'download'])->name('attachments.download')->middleware('auth'); // or your own guards
Route::get('quotationconfig', [App\Http\Controllers\QuotationConfigController::class, 'index'])->name('quotationconfig.index')->middleware('auth');
Route::post('quotationconfig', [App\Http\Controllers\QuotationConfigController::class, 'store'])->name('quotationconfig.store')->middleware('auth');
Route::post('getfpproductsname', [App\Http\Controllers\QuotationController::class, 'getfpproductsname'])->middleware('auth');
Route::post('getprddata', [App\Http\Controllers\QuotationController::class, 'getprddata'])->middleware('auth');
Route::get('updateexchangerate', [App\Http\Controllers\QuotationController::class, 'updateexchangerate']);


