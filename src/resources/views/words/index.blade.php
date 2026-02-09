@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/words.css') }}">
@endpush

@section('title', '単語一覧')
@section('page_title', '単語一覧')

@section('content')
    {{-- 上段：左にタイトル、右上に小さめ検索 --}}
    <div class="words-toolbar">
        <div class="words-toolbar__left d-flex align-items-center gap-2">
            <h2 class="m-0">単語一覧</h2>
            <a href="{{ route('tags.index') }}" class="btn btn-sm btn-outline-secondary">
                🏷 タグ管理
            </a>
        </div>

        <form method="GET" action="{{ route('words.index') }}" class="words-search-mini">
            <input
                type="text"
                name="q"
                value="{{ $q }}"
                class="form-control form-control-sm words-search-mini__q"
                placeholder="検索"
            >

            <select name="tag" class="form-select form-select-sm words-search-mini__tag">
                <option value="">タグ</option>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected((string) $tagId === (string) $tag->id)>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-sm btn-secondary">検索</button>
            <a href="{{ route('words.index') }}" class="btn btn-sm btn-outline-secondary">クリア</a>

            {{-- 並び順（ラジオ：切替で自動送信） --}}
            <div class="words-sort">
                <span class="text-muted small words-sort__label">並び順：</span>

                <div class="btn-group btn-group-sm" role="group" aria-label="並び順">
                    <input
                        type="radio"
                        class="btn-check"
                        name="sort"
                        id="sort-latest"
                        value="latest"
                        @checked(($sort ?? 'latest') === 'latest')
                        onchange="this.form.submit()"
                    >
                    <label class="btn btn-outline-secondary" for="sort-latest">新しい順</label>

                    <input
                        type="radio"
                        class="btn-check"
                        name="sort"
                        id="sort-oldest"
                        value="oldest"
                        @checked(($sort ?? 'latest') === 'oldest')
                        onchange="this.form.submit()"
                    >
                    <label class="btn btn-outline-secondary" for="sort-oldest">古い順</label>
                </div>
            </div>
        </form>
    </div>

    {{-- 単語をその場で追加するフォーム（一覧の上） --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="POST" action="{{ route('words.store') }}" class="row g-2 align-items-start">
                @csrf

                <div class="col-md-2">
                    <label class="form-label small mb-1">単語</label>
                    <input type="text" name="term" class="form-control" value="{{ old('term') }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">読み方（任意）</label>
                    <input
                        type="text"
                        name="reading"
                        class="form-control"
                        value="{{ old('reading') }}"
                        placeholder="例: ららべる"
                    >
                </div>

                <div class="col-md-4">
                    <label class="form-label small mb-1">意味</label>
                    <input type="text" name="meaning" class="form-control" value="{{ old('meaning') }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">メモ（任意）</label>
                    <input type="text" name="note" class="form-control" value="{{ old('note') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small mb-1">新タグ（任意）</label>
                    <input
                        type="text"
                        name="new_tag_name"
                        class="form-control"
                        value="{{ old('new_tag_name') }}"
                        placeholder="例: Laravel"
                    >
                </div>

                {{-- 既存タグ（チェック） --}}
                <div class="col-12">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="form-label small mb-0">既存タグ</span>
                        <span class="text-muted small">（複数OK）</span>
                    </div>

                    <div class="tag-checklist">
                        @forelse ($tags as $tag)
                            <label class="form-check tag-checklist__item">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="tags[]"
                                    value="{{ $tag->id }}"
                                    @checked(in_array($tag->id, old('tags', [])))
                                >
                                <span class="form-check-label">{{ $tag->name }}</span>
                            </label>
                        @empty
                            <div class="text-muted small">タグがまだありません</div>
                        @endforelse
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>
    </div>

    {{-- 一覧 --}}
    <ul class="list-group words-list">
        @forelse ($words as $word)
            <li class="list-group-item words-item">
                <div class="row align-items-start">
                    {{-- 単語（読み方を上に小さく表示） --}}
                    <div class="col-md-2 word-term">
                        @if ($word->reading)
                            <div class="word-reading">{{ $word->reading }}</div>
                        @endif
                        <div class="word-term-main">{{ $word->term }}</div>
                    </div>

                    {{-- 意味（メイン） --}}
                    <div class="col-md-4 word-meaning">
                        {{ $word->meaning }}
                    </div>

                    {{-- メモ --}}
                    <div class="col-md-2 word-note">
                        @if ($word->note)
                            📝 {{ $word->note }}
                        @else
                            <span class="text-muted small">ー</span>
                        @endif
                    </div>

                    {{-- タグ --}}
                    <div class="col-md-2 word-tags">
                        @if ($word->tags->isEmpty())
                            <span class="text-muted small">タグなし</span>
                        @else
                            @foreach ($word->tags as $t)
                                <span class="badge text-bg-light border">{{ $t->name }}</span>
                            @endforeach
                        @endif
                    </div>

                    {{-- 操作 --}}
                    <div class="col-md-2 word-actions text-md-end">
                        <a href="{{ route('words.edit', $word) }}" class="btn btn-sm btn-outline-primary mb-1">編集</a>

                        <form action="{{ route('words.destroy', $word) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('本当に削除しますか？')"
                            >
                                削除
                            </button>
                        </form>
                    </div>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">まだ単語がありません</li>
        @endforelse
    </ul>

    <div class="mt-3 d-flex justify-content-center words-pagination">
        {{ $words->links() }}
    </div>

    @if ($words->total() > 0)
        <div class="text-muted small text-center mt-2 words-count">
            {{ $words->firstItem() }}〜{{ $words->lastItem() }} 件 / 全 {{ $words->total() }} 件
        </div>
    @endif
@endsection