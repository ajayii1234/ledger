<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockPriceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




Route::middleware('auth')->group(function() {

    // CSV import endpoint
Route::post('receipts/import', [ReceiptController::class, 'import'])
->name('receipts.import');

Route::resource('receipts', ReceiptController::class)->except(['edit','update']);


Route::resource('stocks', StockController::class)->only(['index','create','store','show']);
Route::post('stocks/{stock}/prices', [StockPriceController::class,'store'])->name('stocks.prices.store');


Route::get('report', [ReceiptController::class, 'report'])->name('receipts.report');
Route::post('receipts/report/export', [ReceiptController::class, 'exportReport'])->name('receipts.report.export');

Route::get('stocks/{stock}/edit', [StockController::class, 'edit'])
    ->name('stocks.edit');

// Update action
Route::put('stocks/{stock}', [StockController::class, 'update'])
    ->name('stocks.update');

    Route::delete('stocks/{stock}', [StockController::class, 'destroy'])
    ->name('stocks.destroy');
    

Route::get('/ocr', [OcrController::class, 'index'])->name('ocr.index');
Route::post('/ocr/scan', [OcrController::class, 'scan'])->name('ocr.scan');
Route::get('/ocr/results/{id}', [OcrController::class, 'show'])->name('ocr.show');


});

// It worked
   

require __DIR__.'/auth.php';
