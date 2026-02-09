@extends('layouts.app')

@section('title', 'タグ編集')
@section('page_title', 'タグ編集')

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

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('wordbooks.tags.update', [$wordbook, $tag]) }}" class="card card-body">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label">タグ名</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $tag->name) }}">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">更新</button>
        <a href="{{ route('wordbooks.tags.index', $wordbook) }}" class="btn btn-outline-secondary">戻る</a>
    </div>
</form>
@endsection