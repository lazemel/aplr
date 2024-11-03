<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    return view('search');
});

Route::get('/documents/search', [DocumentController::class, 'search'])->name('documents.search');
