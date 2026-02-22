@extends('layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/words.css') }}">
@endpush

@section('title', '単語編集')
@section('page_title', '単語編集')

@section('content')

<div class="notebook-shell">

    {{-- タブも同じにしたい場合はここに追加可 --}}

    <div class="notebook-page">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-body">

                <h2 class="mb-3">✏️ 単語編集</h2>

                <form method="POST"
                      action="{{ route('wordbooks.words.update', [$wordbook, $word]) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">単語</label>
                        <input type="text"
                               name="term"
                               class="form-control"
                               value="{{ old('term', $word->term) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">読み方（任意）</label>
                        <input type="text"
                               name="reading"
                               class="form-control"
                               value="{{ old('reading', $word->reading) }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">意味</label>
                        <textarea name="meaning"
                                  class="form-control"
                                  rows="5">{{ old('meaning', $word->meaning) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">メモ</label>
                        <textarea name="note"
                                  class="form-control"
                                  rows="5">{{ old('note', $word->note) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">タグ（複数選択）</label>
                        @forelse($tags as $tag)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="tags[]"
                                       value="{{ $tag->id }}"
                                       @checked(in_array($tag->id, old('tags', $selectedTagIds)))>
                                <label class="form-check-label">{{ $tag->name }}</label>
                            </div>
                        @empty
                            <p class="text-muted">タグはまだありません</p>
                        @endforelse
                    </div>

                    <div class="mb-3">
                        <label class="form-label">新しいタグを追加（任意）</label>
                        <input type="text"
                               name="new_tag_name"
                               class="form-control"
                               value="{{ old('new_tag_name') }}">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">更新</button>
                        <a href="{{ route('wordbooks.words.index', $wordbook) }}"
                           class="btn btn-outline-secondary">戻る</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

@endsection