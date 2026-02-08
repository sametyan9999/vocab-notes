@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/words.css') }}">
@endpush

@section('title', '単語一覧')
@section('page_title', '単語一覧')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex gap-2">
            <a href="{{ route('words.create') }}" class="btn btn-primary">＋ 新規作成</a>
            <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">🏷 タグ管理</a>
        </div>
    </div>

    <form method="GET" action="{{ route('words.index') }}" class="row g-2 mb-4 words-search">
        <div class="col-md-4">
            <input type="text" name="q" value="{{ $q }}" class="form-control"
                   placeholder="キーワード（単語/意味/メモ）">
        </div>

        <div class="col-md-3">
            <select name="tag" class="form-select">
                <option value="">タグ指定なし</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" @selected((string)$tagId === (string)$tag->id)>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="per_page" class="form-select">
                @foreach([10,20,50] as $n)
                    <option value="{{ $n }}" @selected((int)request('per_page', 10) === $n)>
                        {{ $n }}件
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary flex-grow-1">検索</button>
            <a href="{{ route('words.index') }}" class="btn btn-outline-secondary">クリア</a>
        </div>
    </form>

    <ul class="list-group words-list">
        @forelse($words as $word)
            <li class="list-group-item words-item">
                <div class="row align-items-start">

                    {{-- 単語 --}}
                    <div class="col-md-2 word-term">
                        {{ $word->term }}
                    </div>

                    {{-- 意味（メイン） --}}
                    <div class="col-md-6 word-meaning">
                        {{ $word->meaning }}
                    </div>

                    {{-- メモ（右寄せ） --}}
                    {{-- メモ --}}
                    <div class="col-md-2 word-note">
                        @if($word->note)
                            📝 {{ $word->note }}
                        @else
                            <span class="text-muted small">ー</span>
                        @endif
                    </div>

                    {{-- タグ（右寄せ） --}}
                    <div class="col-md-1 word-tags">
                        @if($word->tags->isEmpty())
                            <span class="text-muted small">タグなし</span>
                        @else
                            @foreach($word->tags as $t)
                                <span class="badge text-bg-light border">{{ $t->name }}</span>
                            @endforeach
                        @endif
                    </div>

                    {{-- 操作ボタン --}}
                    <div class="col-md-1 word-actions text-md-end">
                        <a href="{{ route('words.edit', $word) }}"
                           class="btn btn-sm btn-outline-primary mb-1">編集</a>

                        <form action="{{ route('words.destroy', $word) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('本当に削除しますか？')">
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

    @if($words->total() > 0)
        <div class="text-muted small text-center mt-2 words-count">
            {{ $words->firstItem() }}〜{{ $words->lastItem() }} 件 / 全 {{ $words->total() }} 件
        </div>
    @endif
@endsection