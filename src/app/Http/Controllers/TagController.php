<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function index()
    {
        // タグ一覧（単語に使われている数も一緒に取得）
        $tags = Tag::withCount('words')->orderBy('name')->get();

        return view('tags.index', compact('tags'));
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