<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>単語登録</title>
</head>
<body>
<h1>単語登録</h1>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form method="POST" action="{{ route('words.store') }}">
    @csrf

    <div>
        <label>単語</label><br>
        <input type="text" name="term" value="{{ old('term') }}">
    </div>

    <div>
        <label>意味</label><br>
        <input type="text" name="meaning" value="{{ old('meaning') }}">
    </div>

    <div>
        <label>メモ</label><br>
        <textarea name="note">{{ old('note') }}</textarea>
    </div>

    <div>
    <label>タグ（複数選択）</label><br>

    @forelse($tags as $tag)
        <label>
            <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                @checked(in_array($tag->id, old('tags', [])))>
            {{ $tag->name }}
        </label><br>
    @empty
        <p>タグはまだありません</p>
    @endforelse
</div>

<div>
    <label>新しいタグを追加（任意）</label><br>
    <input type="text" name="new_tag_name" value="{{ old('new_tag_name') }}" placeholder="例：TOEIC">
</div>

    <button type="submit">保存</button>
</form>

<p><a href="{{ route('words.index') }}">一覧へ戻る</a></p>
</body>
</html>