<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CetakRegistrasiAnakController;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return view('welcome');
});

/**
 * Cetak Registrasi Anak
 */
Route::get('/cetak/registrasi-anak/{id}', [CetakRegistrasiAnakController::class, 'cetak'])
    ->name('cetak.registrasi-anak');

/**
 * Invoice
 */
Route::get('/invoice/{registrasi_id}/print', [InvoiceController::class, 'print'])
    ->name('invoice.print');

/**
 * Kirim Invoice ke Email User
 */
Route::post('/invoice/{registrasi_id}/send-email', [InvoiceController::class, 'sendEmail'])
    ->name('invoice.send-email');

Route::get('/cetak/antrian/{id}', [CetakRegistrasiAnakController::class, 'cetakAntrian']);

