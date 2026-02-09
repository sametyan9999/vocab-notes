<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WordController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\WordbookController;
use App\Models\Wordbook;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // 最初の単語帳（シート）へ自動リダイレクト
    $wordbook = Wordbook::orderBy('id')->first();

    // なければ作る（migrate:fresh直後の404回避）
    if (!$wordbook) {
        $wordbook = Wordbook::create([
            'name' => '単語帳',
        ]);
    }

    return redirect()->route('wordbooks.words.index', $wordbook);
});

/*
|--------------------------------------------------------------------------
| Wordbook（シート）内の単語CRUD
|--------------------------------------------------------------------------
*/

Route::get('/wordbooks/{wordbook}/words', [WordController::class, 'index'])
    ->name('wordbooks.words.index');

Route::post('/wordbooks/{wordbook}/words', [WordController::class, 'store'])
    ->name('wordbooks.words.store');

Route::get('/wordbooks/{wordbook}/words/{word}/edit', [WordController::class, 'edit'])
    ->name('wordbooks.words.edit');

Route::put('/wordbooks/{wordbook}/words/{word}', [WordController::class, 'update'])
    ->name('wordbooks.words.update');

Route::delete('/wordbooks/{wordbook}/words/{word}', [WordController::class, 'destroy'])
    ->name('wordbooks.words.destroy');

/*
|--------------------------------------------------------------------------
| Tag 管理
|--------------------------------------------------------------------------
*/

Route::get('/wordbooks/{wordbook}/tags', [TagController::class, 'index'])->name('wordbooks.tags.index');
Route::post('/wordbooks/{wordbook}/tags', [TagController::class, 'store'])->name('wordbooks.tags.store');
Route::get('/wordbooks/{wordbook}/tags/{tag}/edit', [TagController::class, 'edit'])->name('wordbooks.tags.edit');
Route::patch('/wordbooks/{wordbook}/tags/{tag}', [TagController::class, 'update'])->name('wordbooks.tags.update');
Route::delete('/wordbooks/{wordbook}/tags/{tag}', [TagController::class, 'destroy'])->name('wordbooks.tags.destroy');

Route::get('/wordbooks/{wordbook}/edit', [WordbookController::class, 'edit'])->name('wordbooks.edit');
Route::patch('/wordbooks/{wordbook}', [WordbookController::class, 'update'])->name('wordbooks.update');
Route::post('/wordbooks', [WordbookController::class, 'store'])
    ->name('wordbooks.store');
Route::delete('/wordbooks/{wordbook}', [WordbookController::class, 'destroy'])
    ->name('wordbooks.destroy');
Route::post('/wordbooks/reorder', [WordbookController::class, 'reorder'])
    ->name('wordbooks.reorder');