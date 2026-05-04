<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::home');
Route::livewire('/curso/{slug}', 'pages::courseview')->name('cursos.show');