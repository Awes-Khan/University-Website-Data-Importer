<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExcelImportController;

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

Route::post('/excel-import', [ExcelImportController::class, 'upload'])->name('excel.import');
Route::get('/import-data/{id}', [ExcelImportController::class, 'runSeeder'])->name('runSeeder');
Route::get('/process-data/{id}', [ExcelImportController::class, 'processData'])->name('processData');

Route::get('/import', function () {
    return view('importer');
})->name('import');


