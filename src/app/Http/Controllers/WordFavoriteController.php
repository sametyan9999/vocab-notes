<?php

namespace App\Http\Controllers;

use App\Models\Word;
use App\Models\Wordbook;

class WordFavoriteController extends Controller
{
    public function toggle(Wordbook $wordbook, Word $word)
    {
        abort_unless($word->wordbook_id === $wordbook->id, 404);

        $word->is_favorite = ! (bool) $word->is_favorite;
        $word->save();

        return back();
    }
}