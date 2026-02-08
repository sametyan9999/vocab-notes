@extends('layouts.app')

@section('title', 'タグ編集')
@section('page_title', 'タグ編集')

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

<form method="POST" action="{{ route('tags.update', $tag) }}" class="card card-body">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label">タグ名</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $tag->name) }}">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">更新</button>
        <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">戻る</a>
    </div>
</form>
@endsection