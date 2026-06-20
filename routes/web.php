<?php

use App\Http\Controllers\PodcastProxyController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::livewire('/', 'pages::home');
Route::livewire('/curso/{slug}', 'pages::courseview')->name('cursos.show');
Route::get('/cuenta-suspendida', function () {  return view('errors.suspended'); })->name('suspended');


Route::get('/podcasts/{podcast}/stream', [PodcastProxyController::class, 'stream'])
    ->name('podcasts.stream');

