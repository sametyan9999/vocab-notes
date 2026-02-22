@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/words.css') }}">
@endpush

@section('title', 'ランダム出題')
@section('page_title', 'ランダム出題')

@section('content')

<div class="notebook-shell">
    <div class="notebook-page">
        <div class="card">
            <div class="card-body">

                @if(!$word)

                    @if(($quizState ?? '') === 'finished')
                        <div class="alert alert-info mb-3">出題が終了しました。</div>

                        <a class="btn btn-primary"
                           href="{{ request()->fullUrlWithQuery(['restart' => 1]) }}">
                            もう一度出題する
                        </a>

                        <a href="{{ route('wordbooks.words.index', $wordbook) }}"
                           class="btn btn-outline-secondary ms-2">
                            戻る
                        </a>
                    @else
                        <div class="alert alert-warning mb-3">条件に合う単語がありません。</div>

                        <a href="{{ route('wordbooks.words.index', $wordbook) }}"
                           class="btn btn-outline-secondary">
                            戻る
                        </a>
                    @endif

                @else

                    {{-- ループ切り替え --}}
                    <form method="POST"
                          action="{{ route('wordbooks.words.quiz.loop', $wordbook) }}"
                          class="d-inline">
                        @csrf
                        <input type="hidden" name="loop"
                               value="{{ ($loop ?? '') === '1' ? 0 : 1 }}">

                        <button type="submit"
                                class="btn btn-sm {{ ($loop ?? '') === '1'
                                    ? 'btn-warning'
                                    : 'btn-outline-secondary' }}">
                            🔁 {{ ($loop ?? '') === '1'
                                ? 'ループ中（OFF）'
                                : 'ループON' }}
                        </button>
                    </form>

                    @php
                        $modeLabel = match($mode ?? 'all') {
                            'fav' => '★ お気に入りのみ',
                            'tag' => 'タグ別',
                            default => '全部',
                        };

                        $tagName = null;
                        if (!empty($tagId ?? null)) {
                            $tagName = optional($tags->firstWhere('id', (int)$tagId))->name;
                        }
                    @endphp

                    <div class="mb-3">
                        <div class="small text-muted mb-1">出題条件</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge text-bg-light border">モード：{{ $modeLabel }}</span>

                            @if(!empty($q))
                                <span class="badge text-bg-light border">検索：「{{ $q }}」</span>
                            @endif

                            @if(!empty($tagId))
                                <span class="badge text-bg-light border">タグ：{{ $tagName ?? '選択中' }}</span>
                            @endif

                            @if(($fav ?? '') === '1')
                                <span class="badge text-bg-light border">★ お気に入りON</span>
                            @endif

                            @if(($loop ?? '') === '1')
                                <span class="badge text-bg-light border">🔁 ループON</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2 text-muted small">問題</div>

                    <h3 class="mb-3">{{ $word->term }}</h3>

                    @if($word->reading)
                        <div class="text-muted mb-3">{{ $word->reading }}</div>
                    @endif

                    <details class="mb-3">
                        <summary class="btn btn-sm btn-outline-secondary">答えを表示</summary>
                        <div class="mt-2">
                            <div class="fw-bold">意味</div>
                            <div>{!! nl2br(e($word->meaning)) !!}</div>

                            @if($word->note)
                                <div class="fw-bold mt-2">メモ</div>
                                <div>{!! nl2br(e($word->note)) !!}</div>
                            @endif
                        </div>
                    </details>

                    <div class="d-flex gap-2">
                        <a href="{{ route('wordbooks.words.quiz', [
                                'wordbook' => $wordbook->id,
                                'q' => $q,
                                'tag' => $tagId,
                                'fav' => $fav,
                                'mode' => $mode,
                                'next' => 1
                            ]) }}"
                           class="btn btn-primary">
                            次の問題
                        </a>

                        <a href="{{ route('wordbooks.words.index', $wordbook) }}"
                           class="btn btn-outline-secondary">
                            一覧へ
                        </a>
                    </div>

                @endif

            </div>
        </div>
    </div>
</div>

@endsection