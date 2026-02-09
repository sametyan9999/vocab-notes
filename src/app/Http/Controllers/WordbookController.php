<?php

namespace App\Http\Controllers;

use App\Models\Wordbook;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WordbookController extends Controller
{
    // 単語帳 新規作成
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $name = trim($validated['name'] ?? '');
        if ($name === '') {
            $name = '単語帳';
        }

        $maxOrder  = (int) Wordbook::max('sort_order');
        $nextOrder = $maxOrder + 1;

        $wordbook = Wordbook::create([
            'name'       => $name,
            'sort_order' => $nextOrder,
        ]);

        return redirect()->route('wordbooks.words.index', $wordbook);
    }

    public function edit(Wordbook $wordbook)
    {
        $wordbooks = Wordbook::orderBy('sort_order')->orderBy('id')->get();
        return view('wordbooks.edit', compact('wordbook', 'wordbooks'));
    }

    public function update(Request $request, Wordbook $wordbook)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $wordbook->update([
            'name' => trim($validated['name']),
        ]);

        return redirect()->route('wordbooks.words.index', $wordbook);
    }

    // 単語帳削除
    public function destroy(Wordbook $wordbook)
    {
        DB::transaction(function () use ($wordbook) {

            $words = $wordbook->words()->with('tags')->get();

            foreach ($words as $word) {
                $word->tags()->detach();
            }

            $wordbook->words()->delete();
            Tag::where('wordbook_id', $wordbook->id)->delete();
            $wordbook->delete();
        });

        $next = Wordbook::orderBy('sort_order')->orderBy('id')->first();

        return $next
            ? redirect()->route('wordbooks.words.index', $next)
            : redirect('/');
    }

    // ⭐ 並び替え保存（ドラッグ＆ドロップ）
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array'],
            'ids.*' => ['integer', 'exists:wordbooks,id'],
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['ids'] as $index => $id) {
                Wordbook::where('id', $id)->update([
                    'sort_order' => $index + 1,
                ]);
            }
        });

        return response()->json(['ok' => true]);
    }
}