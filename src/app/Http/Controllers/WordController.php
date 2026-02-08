<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWordRequest;
use App\Http\Requests\UpdateWordRequest;
use App\Models\Tag;
use App\Models\Word;
use Illuminate\Http\Request;

class WordController extends Controller
{
    public function index(Request $request)
    {
        // 検索キーワードとタグID、並び順を取得
        $q     = $request->query('q');
        $tagId = $request->query('tag');
        $sort  = $request->query('sort', 'latest'); // latest / oldest / reading / term

        // 単語取得の準備（タグも一緒に）
        $wordsQuery = Word::query()->with('tags');

        // キーワード検索
        if ($q) {
            $wordsQuery->where(function ($query) use ($q) {
                $query->where('term', 'like', "%{$q}%")
                      ->orWhere('reading', 'like', "%{$q}%")
                      ->orWhere('meaning', 'like', "%{$q}%")
                      ->orWhere('note', 'like', "%{$q}%");
            });
        }

        // タグ絞り込み
        if ($tagId) {
            $wordsQuery->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        // 並び順（ここで確定）
        switch ($sort) {
            case 'oldest':
                $wordsQuery->oldest(); // created_at 昇順
                break;

            case 'reading':
                // あいうえお順（reading が空のものは後ろに回したい場合）
                // DBがMySQLなら orderByRaw が使いやすい
                $wordsQuery
                    ->orderByRaw("CASE WHEN reading IS NULL OR reading = '' THEN 1 ELSE 0 END")
                    ->orderBy('reading')
                    ->orderBy('term');
                break;

            case 'term':
                // 単語（term）での文字列順（英単語を入れる想定なら便利）
                $wordsQuery->orderBy('term');
                break;

            default:
                $wordsQuery->latest(); // created_at 降順
                break;
        }

        // データ取得（ページネーション）
        $perPage = (int) $request->query('per_page', 10);
        $words   = $wordsQuery->paginate($perPage)->withQueryString();
        $tags    = Tag::orderBy('name')->get();

        // 一覧画面へ（sort も渡す）
        return view('words.index', compact('words', 'tags', 'q', 'tagId', 'sort', 'perPage'));
    }

    public function create()
    {
        $tags = Tag::orderBy('name')->get();
        return view('words.create', compact('tags'));
    }

    public function store(StoreWordRequest $request)
    {
        $validated = $request->validated();

        $word = Word::create([
            'term'    => $validated['term'],
            'reading' => $validated['reading'] ?? null,
            'meaning' => $validated['meaning'],
            'note'    => $validated['note'] ?? null,
        ]);

        $tagIds = collect($validated['tags'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $newTagName = trim($validated['new_tag_name'] ?? '');
        if ($newTagName !== '') {
            $tag = Tag::firstOrCreate(['name' => $newTagName]);
            $tagIds = $tagIds->push($tag->id)->unique()->values();
        }

        $word->tags()->sync($tagIds->all());

        return redirect()->route('words.index');
    }

    public function edit(Word $word)
    {
        $tags = Tag::orderBy('name')->get();
        $selectedTagIds = $word->tags()->pluck('tags.id')->all();

        return view('words.edit', compact('word', 'tags', 'selectedTagIds'));
    }

    public function update(UpdateWordRequest $request, Word $word)
    {
        $validated = $request->validated();

        $word->update([
            'term'    => $validated['term'],
            'reading' => $validated['reading'] ?? null,
            'meaning' => $validated['meaning'],
            'note'    => $validated['note'] ?? null,
        ]);

        $tagIds = collect($validated['tags'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $newTagName = trim($validated['new_tag_name'] ?? '');
        if ($newTagName !== '') {
            $tag = Tag::firstOrCreate(['name' => $newTagName]);
            $tagIds = $tagIds->push($tag->id)->unique()->values();
        }

        $word->tags()->sync($tagIds->all());

        return redirect()->route('words.index')->with('success', '更新しました');
    }

    public function destroy(Word $word)
    {
        $word->tags()->detach();
        $word->delete();

        return redirect()->route('words.index');
    }
}