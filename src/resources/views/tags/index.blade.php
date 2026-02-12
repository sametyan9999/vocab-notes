@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/words.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tags.css') }}">
@endpush

@section('title', 'タグ管理')
@section('page_title', 'タグ管理')

@section('content')

    {{-- シート（単語帳）切替タブ --}}
    <div class="mb-3 d-flex gap-2 flex-wrap">
        @foreach ($wordbooks as $wb)
            <a
                href="{{ route('wordbooks.tags.index', $wb) }}"
                class="btn btn-sm {{ $wb->id === $wordbook->id ? 'btn-primary' : 'btn-outline-primary' }}"
            >
                {{ $wb->name }}
            </a>
        @endforeach
    </div>

    {{-- タグ追加フォーム --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('wordbooks.tags.store', $wordbook) }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="新しいタグ名" value="{{ old('name') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">タグ追加</button>
                </div>
            </form>
        </div>
    </div>

    {{-- タグ一覧 --}}
    <ul class="list-group">
        @forelse($tags as $tag)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ route('wordbooks.words.index', ['wordbook' => $wordbook->id, 'tag' => $tag->id]) }}" class="tag-link">
                        {{ $tag->name }}
                    </a>
                    <span class="text-muted small ms-2">
                        （使用中：{{ $tag->words_count }}件）
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('wordbooks.tags.edit', [$wordbook, $tag]) }}" class="btn btn-sm btn-outline-primary">
                        編集
                    </a>

                    <form action="{{ route('wordbooks.tags.destroy', [$wordbook, $tag]) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        @php
                            $msg = $tag->words_count > 0
                                ? "このタグは使用中（{$tag->words_count}件）です。紐づけも外して削除しますか？"
                                : "タグ「{$tag->name}」を削除しますか？";
                        @endphp

                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm(@js($msg))"
                        >
                            削除
                        </button>
                    </form>
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted">タグがまだありません</li>
        @endforelse
    </ul>

    <div class="mt-4">
        <a href="{{ route('wordbooks.words.index', $wordbook) }}" class="btn btn-outline-secondary">← 単語一覧へ戻る</a>
    </div>
@endsection