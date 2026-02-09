@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tags.css') }}">
@endpush

@section('title', 'タグ管理')
@section('page_title', 'タグ管理')

@section('content')
    {{-- タグ追加フォーム --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('tags.store') }}" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-6">
                    <input type="text" name="name" class="form-control" placeholder="新しいタグ名">
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
                    <a href="{{ route('words.index', ['tag' => $tag->id]) }}" class="tag-link">
                        {{ $tag->name }}
                    </a>
                    <span class="text-muted small ms-2">
                        （使用中：{{ $tag->words_count }}件）
                    </span>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('tags.edit', $tag) }}" class="btn btn-sm btn-outline-primary">
                        編集
                    </a>

                    <form action="{{ route('tags.destroy', $tag) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('タグ「{{ $tag->name }}」を削除しますか？')"
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
        <a href="{{ route('words.index') }}" class="btn btn-outline-secondary">← 単語一覧へ戻る</a>
    </div>
@endsection