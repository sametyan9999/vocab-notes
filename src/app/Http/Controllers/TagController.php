<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index(Request $request)
{
    $tags = Tag::withCount('words')->orderBy('name')->get();

    // 単語一覧の絞り込みと同じクエリキー（tag）を受け取る
    $tagId = $request->query('tag');

    return view('tags.index', compact('tags', 'tagId'));
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Tag::firstOrCreate([
            'name' => trim($validated['name']),
        ]);

        return redirect()->route('tags.index')->with('success', 'タグを追加しました');
    }
public function edit(Tag $tag)
{
    return view('tags.edit', compact('tag'));
}

public function update(Request $request, Tag $tag)
{
    // タグ名のチェック
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
    ]);

    $name = trim($validated['name']);

    // 同名タグが既にあるなら更新させない（重複防止）
    $exists = Tag::where('name', $name)->where('id', '!=', $tag->id)->exists();
    if ($exists) {
        return back()
            ->withErrors(['name' => '同じ名前のタグが既に存在します。'])
            ->withInput();
    }

    $tag->update(['name' => $name]);

    return redirect()->route('tags.index')->with('success', 'タグ名を更新しました');
}
    public function destroy(Tag $tag)
    {
        // 1件でも単語に使われていたら削除禁止
        if ($tag->words()->exists()) {
            return redirect()->route('tags.index')
                ->with('success', 'このタグは使用中のため削除できません');
        }

        $tag->delete();

        return redirect()->route('tags.index')->with('success', 'タグを削除しました');
    }
}