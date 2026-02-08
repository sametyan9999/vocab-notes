@extends('layouts.app')

@section('title', '単語一覧')
@section('page_title', '単語一覧')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex gap-2">
        <a href="{{ route('words.create') }}" class="btn btn-primary">＋ 新規作成</a>
        <a href="{{ route('tags.index') }}" class="btn btn-outline-secondary">🏷 タグ管理</a>
    </div>
</div>



    <form method="GET" action="{{ route('words.index') }}" class="row g-2 mb-4">
        <div class="col-md-5">
            <input type="text" name="q" value="{{ $q }}" class="form-control"
                   placeholder="キーワード（単語/意味/メモ）">
        </div>

        <div class="col-md-4">
            <select name="tag" class="form-select">
                <option value="">タグ指定なし</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" @selected((string)$tagId === (string)$tag->id)>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-secondary flex-grow-1">検索</button>
            <a href="{{ route('words.index') }}" class="btn btn-outline-secondary">クリア</a>
        </div>
    </form>

    <ul class="list-group">
        @forelse($words as $word)
            <li class="list-group-item">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="flex-grow-1">
                        <div class="fw-bold">{{ $word->term }}</div>
                        <div>{{ $word->meaning }}</div>

                        @if($word->note)
                            <div class="text-muted small mt-1">メモ: {{ $word->note }}</div>
                        @endif

                        <div class="small mt-2">
                            タグ:
                            @if($word->tags->isEmpty())
                                <span class="text-muted">なし</span>
                            @else
                                @foreach($word->tags as $t)
                                    <span class="badge text-bg-light border">{{ $t->name }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="text-nowrap">
                        <a href="{{ route('words.edit', $word) }}" class="btn btn-sm btn-outline-primary mb-2">
                            編集
                        </a>

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
@endsection