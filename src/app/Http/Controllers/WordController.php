<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWordRequest;
use App\Http\Requests\UpdateWordRequest;
use App\Models\Tag;
use App\Models\Word;
use App\Models\Wordbook;
use Illuminate\Http\Request;

class WordController extends Controller
{
    public function index(Request $request, Wordbook $wordbook)
    {
        $q     = $request->query('q');
        $tagId = $request->query('tag');
        $sort  = $request->query('sort', 'latest');

        // この単語帳の単語だけ取得
        $wordsQuery = $wordbook->words()->with('tags');

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
            $wordsQuery->whereHas('tags', function ($query) use ($tagId, $wordbook) {
                $query->where('tags.id', $tagId)
                      ->where('tags.wordbook_id', $wordbook->id);
            });
        }

        switch ($sort) {
            case 'oldest':
                $wordsQuery->oldest();
                break;
            case 'term':
                $wordsQuery->orderBy('term');
                break;
            default:
                $wordsQuery->latest();
                break;
        }

        $perPage = (int) $request->query('per_page', 10);
        $words   = $wordsQuery->paginate($perPage)->withQueryString();

        // ★ 並び替え後の順番で単語帳タブを表示
        $wordbooks = Wordbook::orderBy('id')->get();

        // この単語帳のタグだけ表示
        $tags = Tag::where('wordbook_id', $wordbook->id)
            ->orderBy('name')
            ->get();

        return view('words.index', compact(
            'wordbook',
            'wordbooks',
            'words',
            'tags',
            'q',
            'tagId',
            'sort',
            'perPage'
        ));
    }

    public function store(StoreWordRequest $request, Wordbook $wordbook)
    {
        $validated = $request->validated();

        $word = $wordbook->words()->create([
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
            $tag = Tag::firstOrCreate([
                'wordbook_id' => $wordbook->id,
                'name' => $newTagName,
            ]);
            $tagIds = $tagIds->push($tag->id)->unique()->values();
        }

        $tagIds = Tag::where('wordbook_id', $wordbook->id)
            ->whereIn('id', $tagIds->all())
            ->pluck('id')
            ->all();

        $word->tags()->sync($tagIds);

        return redirect()->route('wordbooks.words.index', $wordbook);
    }

    public function edit(Wordbook $wordbook, Word $word)
    {
        abort_unless($word->wordbook_id === $wordbook->id, 404);

        $tags = Tag::where('wordbook_id', $wordbook->id)
            ->orderBy('name')
            ->get();

        $selectedTagIds = $word->tags()->pluck('tags.id')->all();

        // ★ ここも sort_order に統一
        $wordbooks = Wordbook::orderBy('sort_order')->orderBy('id')->get();

        return view('words.edit', compact('wordbook', 'wordbooks', 'word', 'tags', 'selectedTagIds'));
    }

    public function update(UpdateWordRequest $request, Wordbook $wordbook, Word $word)
    {
        abort_unless($word->wordbook_id === $wordbook->id, 404);

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
            $tag = Tag::firstOrCreate([
                'wordbook_id' => $wordbook->id,
                'name' => $newTagName,
            ]);
            $tagIds = $tagIds->push($tag->id)->unique()->values();
        }

        $tagIds = Tag::where('wordbook_id', $wordbook->id)
            ->whereIn('id', $tagIds->all())
            ->pluck('id')
            ->all();

        $word->tags()->sync($tagIds);

        return redirect()->route('wordbooks.words.index', $wordbook)->with('success', '更新しました');
    }

    public function destroy(Wordbook $wordbook, Word $word)
    {
        abort_unless($word->wordbook_id === $wordbook->id, 404);

        $word->tags()->detach();
        $word->delete();

        return redirect()->route('wordbooks.words.index', $wordbook);
    }
}