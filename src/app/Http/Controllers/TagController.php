<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Wordbook;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request, Wordbook $wordbook)
    {
        // この単語帳のタグだけ
        $tags = Tag::where('wordbook_id', $wordbook->id)
            ->withCount('words')
            ->orderBy('name')
            ->get();

        // 単語一覧の絞り込みと同じクエリキー（tag）を受け取る
        $tagId = $request->query('tag');

        // タブ用（全単語帳）
        $wordbooks = Wordbook::orderBy('name')->get();

        return view('tags.index', compact('tags', 'tagId', 'wordbook', 'wordbooks'));
    }

    public function store(Request $request, Wordbook $wordbook)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Tag::firstOrCreate([
            'wordbook_id' => $wordbook->id,
            'name' => trim($validated['name']),
        ]);

        return redirect()->route('wordbooks.tags.index', $wordbook)->with('success', 'タグを追加しました');
    }

    public function edit(Wordbook $wordbook, Tag $tag)
    {
        // URLの単語帳とタグの所属が違うなら404
        abort_unless($tag->wordbook_id === $wordbook->id, 404);

        $wordbooks = Wordbook::orderBy('name')->get();

        return view('tags.edit', compact('tag', 'wordbook', 'wordbooks'));
    }

    public function update(Request $request, Wordbook $wordbook, Tag $tag)
    {
        abort_unless($tag->wordbook_id === $wordbook->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $name = trim($validated['name']);

        // 同じ単語帳内での重複だけ防ぐ
        $exists = Tag::where('wordbook_id', $wordbook->id)
            ->where('name', $name)
            ->where('id', '!=', $tag->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => '同じ名前のタグが既に存在します。'])
                ->withInput();
        }

        $tag->update(['name' => $name]);

        return redirect()->route('wordbooks.tags.index', $wordbook)->with('success', 'タグ名を更新しました');
    }

    public function destroy(Wordbook $wordbook, Tag $tag)
    {
        abort_unless($tag->wordbook_id === $wordbook->id, 404);

        // 使用中でも「確認後に削除できる」方針にするなら、ここでは禁止しない
        // ただし削除するなら、pivotを外してから削除する
        $tag->words()->detach();
        $tag->delete();

        return redirect()->route('wordbooks.tags.index', $wordbook)->with('success', 'タグを削除しました');
    }
}