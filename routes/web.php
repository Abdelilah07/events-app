<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
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


// Route::resource('events', EventController::class)->middleware('check_organizer');
Route::resource('events', EventController::class);

// Route::middleware('check_organizer')->group(function () {
//     Route::resource('events', EventController::class);
//     Route::post('events/{event}/detach/{user}', [EventController::class, 'detachUser'])->name('events.detach');
//     Route::post('events/{event}/sync', [EventController::class, 'syncUsers'])->name('events.sync');
// });

require __DIR__ . '/auth.php';
