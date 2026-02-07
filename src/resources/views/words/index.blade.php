<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>単語一覧</title>
</head>
<body>
<h1>単語一覧</h1>

<p><a href="{{ route('words.create') }}">新規作成</a></p>

<form method="GET" action="{{ route('words.index') }}">
    <input type="text" name="q" value="{{ $q }}" placeholder="キーワード（単語/意味/メモ）">
    <select name="tag">
        <option value="">タグ指定なし</option>
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}" @selected((string)$tagId === (string)$tag->id)>{{ $tag->name }}</option>
        @endforeach
    </select>
    <button type="submit">検索</button>
</form>

<hr>

<ul>
@forelse($words as $word)
    <li>
        <strong>{{ $word->term }}</strong> / {{ $word->meaning }}
        @if($word->note)
            <div>メモ: {{ $word->note }}</div>
        @endif
        <div>
            タグ:
            @if($word->tags->isEmpty())
                なし
            @else
                {{ $word->tags->pluck('name')->join(', ') }}
            @endif
        </div>
    </li>
@empty
    <li>まだ単語がありません</li>
@endforelse
</ul>

</body>
</html>