@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/words.css') }}">
@endpush

@section('title', 'タグ編集')
@section('page_title', 'タグ編集')

@section('content')

<div class="notebook-shell">
    <div class="notebook-page">

        {{-- 単語帳タブ --}}
        <div class="wordbook-tabs mb-3">
            @foreach ($wordbooks as $wb)
                <div class="wordbook-tab-item {{ $wb->id === $wordbook->id ? 'is-active' : '' }}">
                    <a href="{{ route('wordbooks.tags.index', $wb) }}"
                       class="wordbook-tab-btn text-decoration-none">
                        {{ $wb->name }}
                    </a>
                </div>
            @endforeach
        </div>

        {{-- エラー表示 --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 編集カード --}}
        <div class="card">
            <div class="card-body">

                <h2 class="mb-3">🏷 タグ編集</h2>

                <form method="POST"
                      action="{{ route('wordbooks.tags.update', [$wordbook, $tag]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label class="form-label">タグ名</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $tag->name) }}">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            更新
                        </button>

                        <a href="{{ route('wordbooks.tags.index', $wordbook) }}"
                           class="btn btn-outline-secondary">
                            戻る
                        </a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection