<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::home');
Route::livewire('/curso/{slug}', 'pages::courseview')->name('cursos.show');
Route::get('/cuenta-suspendida', function () {
    return view('errors.suspended'); // Crea esta vista en resources/views/errors/suspended.blade.php
})->name('suspended');