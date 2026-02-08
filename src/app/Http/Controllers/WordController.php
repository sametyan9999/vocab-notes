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
        // 検索キーワードとタグIDを取得
        $q = $request->query('q');
        $tagId = $request->query('tag');

        // 単語取得の準備（タグも一緒に・新しい順）
        $wordsQuery = Word::query()->with('tags')->latest();

        // キーワード検索
        if ($q) {
            $wordsQuery->where(function ($query) use ($q) {
                $query->where('term', 'like', "%{$q}%")
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

// データ取得（ページネーション）
$words = $wordsQuery->paginate(10)->withQueryString();
$tags = Tag::orderBy('name')->get();

        // 一覧画面へ
        return view('words.index', compact('words', 'tags', 'q', 'tagId'));
    }

    public function create()
    {
        // チェックボックス表示用のタグ一覧
        $tags = Tag::orderBy('name')->get();

        return view('words.create', compact('tags'));
    }

    public function store(StoreWordRequest $request)
    {
        // バリデーション済みデータのみ取得
        $validated = $request->validated();

        // 単語を保存
        $word = Word::create([
            'term' => $validated['term'],
            'meaning' => $validated['meaning'],
            'note' => $validated['note'] ?? null,
        ]);

        // 既存タグ（チェックされたもの）
        $tagIds = collect($validated['tags'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        // 新規タグが入力されていれば作成または取得
        $newTagName = trim($validated['new_tag_name'] ?? '');
        if ($newTagName !== '') {
            $tag = Tag::firstOrCreate(['name' => $newTagName]);
            $tagIds = $tagIds->push($tag->id)->unique()->values();
        }

        // 単語とタグを紐付け
        $word->tags()->sync($tagIds->all());

        // 一覧へ戻る
        return redirect()->route('words.index');
    }
public function edit(Word $word)
{
    $tags = Tag::orderBy('name')->get();

    // 既に付いているタグID（チェックを付けるため）
    $selectedTagIds = $word->tags()->pluck('tags.id')->all();

    return view('words.edit', compact('word', 'tags', 'selectedTagIds'));
}

public function update(UpdateWordRequest $request, Word $word)
{
    $validated = $request->validated();

    $word->update([
        'term' => $validated['term'],
        'meaning' => $validated['meaning'],
        'note' => $validated['note'] ?? null,
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
        // 単語に紐づいているタグの関係を削除（中間テーブル）
        $word->tags()->detach();

        // 単語自体を削除
        $word->delete();

        // 一覧ページへ戻る
        return redirect()->route('words.index');
    }
}