@extends('layouts.app')

@section('title', '単語編集')
@section('page_title', '単語編集')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('words.update', $word) }}" class="card card-body">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">単語</label>
        <input type="text" name="term" class="form-control" value="{{ old('term', $word->term) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">意味</label>
        <input type="text" name="meaning" class="form-control" value="{{ old('meaning', $word->meaning) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">メモ</label>
        <textarea name="note" class="form-control" rows="3">{{ old('note', $word->note) }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">タグ（複数選択）</label>
        @forelse($tags as $tag)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="tags[]" value="{{ $tag->id }}"
                       @checked(in_array($tag->id, old('tags', $selectedTagIds)))>
                <label class="form-check-label">{{ $tag->name }}</label>
            </div>
        @empty
            <p class="text-muted">タグはまだありません</p>
        @endforelse
    </div>

    <div class="mb-3">
        <label class="form-label">新しいタグを追加（任意）</label>
        <input type="text" name="new_tag_name" class="form-control" value="{{ old('new_tag_name') }}" placeholder="例：TOEIC">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">更新</button>
        <a href="{{ route('words.index') }}" class="btn btn-outline-secondary">戻る</a>
    </div>
</form>
@endsection