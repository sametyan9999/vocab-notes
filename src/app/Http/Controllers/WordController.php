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
        $fav   = $request->query('fav');

        $wordsQuery = $wordbook->words()->with('tags');

        if ($fav === '1') {
            $wordsQuery->where('is_favorite', true);
        }

        if ($q) {
            $wordsQuery->where(function ($query) use ($q) {
                $query->where('term', 'like', "%{$q}%")
                      ->orWhere('reading', 'like', "%{$q}%")
                      ->orWhere('meaning', 'like', "%{$q}%")
                      ->orWhere('note', 'like', "%{$q}%");
            });
        }

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

        $totalCount    = $wordbook->words()->count();
        $filteredCount = $words->count();

        $wordbooks = Wordbook::orderBy('sort_order')->orderBy('id')->get();

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
            'perPage',
            'fav',
            'totalCount',
            'filteredCount'
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

        return redirect()->route('wordbooks.words.index', $wordbook)
                         ->with('success', '更新しました');
    }

    public function destroy(Wordbook $wordbook, Word $word)
    {
        abort_unless($word->wordbook_id === $wordbook->id, 404);

        $word->tags()->detach();
        $word->delete();

        return redirect()->route('wordbooks.words.index', $wordbook);
    }

    public function quiz(Request $request, Wordbook $wordbook)
{
    $q     = $request->query('q');
    $tagId = $request->query('tag');
    $mode  = $request->query('mode', 'all');
    $fav   = $request->query('fav');
    $loop  = session('quiz_loop');

    if ($mode === 'fav') {
        $fav = '1';
    }

    $wordsQuery = $wordbook->words()->with('tags');

    if ($q) {
        $wordsQuery->where(function ($query) use ($q) {
            $query->where('term', 'like', "%{$q}%")
                  ->orWhere('reading', 'like', "%{$q}%")
                  ->orWhere('meaning', 'like', "%{$q}%")
                  ->orWhere('note', 'like', "%{$q}%");
        });
    }

    if ($tagId) {
        $wordsQuery->whereHas('tags', function ($query) use ($tagId, $wordbook) {
            $query->where('tags.id', $tagId)
                  ->where('tags.wordbook_id', $wordbook->id);
        });
    }

    if ($fav === '1') {
        $wordsQuery->where('is_favorite', true);
    }

    $deckKey = 'quiz_deck:' . md5(json_encode([
        'wordbook_id' => $wordbook->id,
        'q'   => (string) $q,
        'tag' => (string) $tagId,
        'fav' => (string) $fav,
        'mode'=> (string) $mode,
    ]));

    $poolIds = (clone $wordsQuery)
        ->reorder()
        ->orderBy('id')
        ->pluck('id')
        ->all();

    if (empty($poolIds)) {
        return view('words.quiz', [
            'wordbook' => $wordbook,
            'word' => null,
            'q' => $q,
            'tagId' => $tagId,
            'fav' => $fav,
            'mode' => $mode,
            'loop' => $loop,
            'tags' => Tag::where('wordbook_id', $wordbook->id)->orderBy('name')->get(),
            'quizState' => 'no_match',
        ]);
    }

    if ($request->boolean('restart')) {
        session()->forget($deckKey);
    }

    $deck = session($deckKey);

    if (!$deck) {
        $deck = $poolIds;
        shuffle($deck);
    }

    if ($request->has('next')) {
        array_shift($deck);
    }

    if (empty($deck)) {
        if ($loop === '1') {
            $deck = $poolIds;
            shuffle($deck);
        } else {
            session()->forget($deckKey);

            return view('words.quiz', [
                'wordbook' => $wordbook,
                'word' => null,
                'q' => $q,
                'tagId' => $tagId,
                'fav' => $fav,
                'mode' => $mode,
                'loop' => $loop,
                'tags' => Tag::where('wordbook_id', $wordbook->id)->orderBy('name')->get(),
                'quizState' => 'finished',
            ]);
        }
    }

    session([$deckKey => $deck]);

    $word = $wordbook->words()->with('tags')->find($deck[0]);

    return view('words.quiz', [
        'wordbook' => $wordbook,
        'word' => $word,
        'q' => $q,
        'tagId' => $tagId,
        'fav' => $fav,
        'mode' => $mode,
        'loop' => $loop,
        'tags' => Tag::where('wordbook_id', $wordbook->id)->orderBy('name')->get(),
        'quizState' => 'playing',
    ]);
}
}