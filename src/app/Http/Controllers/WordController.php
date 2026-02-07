<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $tagId = $request->query('tag');

        $wordsQuery = Word::query()->with('tags')->latest();

        if ($q) {
            $wordsQuery->where(function ($query) use ($q) {
                $query->where('term', 'like', "%{$q}%")
                      ->orWhere('meaning', 'like', "%{$q}%")
                      ->orWhere('note', 'like', "%{$q}%");
            });
        }

        if ($tagId) {
            $wordsQuery->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        $words = $wordsQuery->get();
        $tags = Tag::orderBy('name')->get();

        return view('words.index', compact('words', 'tags', 'q', 'tagId'));
    }

    public function create()
    {
        $tags = Tag::orderBy('name')->get();
        return view('words.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'term' => ['required', 'string', 'max:255'],
            'meaning' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string'],

            // チェックボックス（複数OK、無くてもOK）
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],

            // 新規タグ（1つ追加、空でもOK）
            'new_tag_name' => ['nullable', 'string', 'max:255'],
        ]);

        $word = Word::create([
            'term' => $validated['term'],
            'meaning' => $validated['meaning'],
            'note' => $validated['note'] ?? null,
        ]);

        // 既存タグ（チェックされたID）
        $tagIds = collect($validated['tags'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        // 新規タグ（任意）
        $newTagName = trim($validated['new_tag_name'] ?? '');
        if ($newTagName !== '') {
            $tag = Tag::firstOrCreate(['name' => $newTagName]);
            $tagIds = $tagIds->push($tag->id)->unique()->values();
        }

        // タグなしOK
        $word->tags()->sync($tagIds->all());

        return redirect()->route('words.index');
    }
}