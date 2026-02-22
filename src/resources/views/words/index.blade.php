@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/words.css') }}">
@endpush

@section('title', '単語一覧')
@section('page_title', '単語一覧')

@section('content')

<div class="notebook-shell">

    {{-- =======================
         📑 ノート見出しタブ
    ======================== --}}
    <div id="wordbookTabs"
         class="wordbook-tabs"
         data-reorder-url="{{ route('wordbooks.reorder') }}"
         data-csrf="{{ csrf_token() }}">

        @foreach ($wordbooks as $wb)
            <div class="wordbook-tab-item {{ $wb->id === $wordbook->id ? 'is-active' : '' }}"
                 data-id="{{ $wb->id }}"
                 data-href="{{ route('wordbooks.words.index', $wb) }}">

                <button type="button" class="wordbook-tab-btn wordbook-tab">
                    {{ $wb->name }}
                </button>
            </div>
        @endforeach

        <form method="POST" action="{{ route('wordbooks.store') }}" class="d-inline">
            @csrf
            <input type="hidden" name="name" value="">
            <button type="submit" class="wordbook-tab add-tab">＋</button>
        </form>
    </div>

    <div class="notebook-page">

        <div class="words-topbar d-flex flex-wrap align-items-center gap-2 w-100">

            {{-- 並び順フォーム --}}
            <form method="GET" action="{{ route('wordbooks.words.index', $wordbook) }}"
                  class="ms-auto d-flex flex-wrap align-items-center gap-1">
                <input type="hidden" name="q" value="{{ $q }}">
                <input type="hidden" name="tag" value="{{ $tagId }}">
                <input type="hidden" name="fav" value="{{ $fav }}">

                <span class="text-muted small">並び順：</span>

                <div class="btn-group btn-group-sm" role="group">
                    <input type="radio" class="btn-check" name="sort" id="sort-latest" value="latest"
                           @checked(($sort ?? 'latest') === 'latest') onchange="this.form.submit()">
                    <label class="btn btn-outline-secondary" for="sort-latest">新しい順</label>

                    <input type="radio" class="btn-check" name="sort" id="sort-oldest" value="oldest"
                           @checked(($sort ?? 'latest') === 'oldest') onchange="this.form.submit()">
                    <label class="btn btn-outline-secondary" for="sort-oldest">古い順</label>
                </div>
            </form>

            {{-- 単語帳メニュー --}}
            <div class="dropdown ms-2">
                <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center"
                        type="button"
                        data-bs-toggle="dropdown"
                        style="width:32px; height:32px; padding:0;">
                    <span style="font-size:18px; line-height:1;">⋯</span>
                </button>

                <ul class="dropdown-menu dropdown-menu-end p-3" style="min-width: 260px;">
                    <li class="mb-2">
                        <div class="small text-muted mb-1">単語帳名</div>
                        <form method="POST" action="{{ route('wordbooks.update', $wordbook) }}"
                              class="d-flex gap-2 align-items-center">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="name"
                                   class="form-control form-control-sm"
                                   value="{{ old('name', $wordbook->name) }}">
                            <button type="submit" class="btn btn-sm btn-outline-secondary px-2">保存</button>
                        </form>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <form method="POST" action="{{ route('wordbooks.destroy', $wordbook) }}"
                              onsubmit="return confirm('この単語帳を削除しますか？（中の単語もすべて削除され、元に戻せません。）')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger w-100">
                                この単語帳を削除
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        @if ($errors->has('name'))
            <div class="alert alert-danger py-2">
                {{ $errors->first('name') }}
            </div>
        @endif

        {{-- タイトル＋件数表示＋検索 --}}
<div class="words-toolbar">

    {{-- 左側：タイトル + 件数 + タグ管理 --}}
    <div class="words-toolbar__left d-flex align-items-start gap-3 flex-wrap">
        <div>
            <h2 class="mb-1">単語一覧</h2>
            <div class="text-muted small">
                全 {{ $totalCount }} 件中 {{ $filteredCount }} 件表示
            </div>
        </div>

        <a href="{{ route('wordbooks.tags.index', $wordbook) }}"
           class="btn btn-sm btn-outline-secondary mt-1">
            🏷 タグ管理
        </a>
    </div>

    @php
    $quizBase = [
        'wordbook' => $wordbook->id,
        'q' => $q,
        'tag' => $tagId,
        'fav' => $fav,
    ];
@endphp

<div class="words-toolbar__center">
    <div class="dropdown">

        <button class="btn btn-sm quiz-btn dropdown-toggle"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            🎲 問題出題
        </button>

        <ul class="dropdown-menu dropdown-menu-end p-2" style="min-width: 220px;">
    <li>
        <a class="dropdown-item"
           href="{{ route('wordbooks.words.quiz', [
                'wordbook' => $wordbook,
                'q' => $q,
                'tag' => $tagId,
                'mode' => 'all',
           ]) }}">
            全部（現在の絞り込みを反映）
        </a>
    </li>

    <li>
        <a class="dropdown-item"
           href="{{ route('wordbooks.words.quiz', [
                'wordbook' => $wordbook,
                'q' => $q,
                'tag' => $tagId,
                'mode' => 'fav',
           ]) }}">
            ★ お気に入りのみ（現在の絞り込みを反映）
        </a>
    </li>

    <li><hr class="dropdown-divider my-1"></li>

    <li>
        @if(empty($tagId))
            <span class="dropdown-item text-muted">タグ別（選択中のタグから出題）</span>
            <div class="px-3 pt-1 small text-muted">
                ※先にタグを選択してください
            </div>
        @else
            <a class="dropdown-item"
               href="{{ route('wordbooks.words.quiz', [
                    'wordbook' => $wordbook,
                    'q' => $q,
                    'tag' => $tagId,
                    'mode' => 'tag',
               ]) }}">
                タグ別（選択中のタグから出題）
            </a>
        @endif
    </li>
</ul>

    </div>
</div>

    {{-- 右側：検索フォーム --}}
    <div class="words-toolbar__right">
        <form method="GET" action="{{ route('wordbooks.words.index', $wordbook) }}"
              class="words-search d-flex align-items-center gap-2 flex-wrap">

            <input type="text" name="q" value="{{ $q }}"
                   class="form-control form-control-sm"
                   style="width: 220px;" placeholder="検索">

            <select name="tag" class="form-select form-select-sm" style="width: 180px;">
                <option value="">タグ</option>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected((string)$tagId === (string)$tag->id)>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>

            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="fav" value="{{ $fav }}">

            <button type="submit" class="btn btn-sm btn-secondary">検索</button>

            <a href="{{ route('wordbooks.words.index', $wordbook) }}"
               class="btn btn-sm btn-outline-secondary">クリア</a>

            @if(($fav ?? '') === '1')
                <a href="{{ request()->fullUrlWithQuery(['fav' => null]) }}"
                   class="btn btn-sm btn-warning">★ お気に入り中</a>
            @else
                <a href="{{ request()->fullUrlWithQuery(['fav' => 1]) }}"
                   class="btn btn-sm btn-outline-warning">☆ お気に入り</a>
            @endif

        </form>
    </div>

</div>

        {{-- 単語追加フォーム --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" action="{{ route('wordbooks.words.store', $wordbook) }}"
                      class="row g-2 align-items-start"
                      novalidate>
                    @csrf
                    <div class="col-md-2">
                        <label class="form-label small mb-1">単語</label>
                        <input type="text" name="term" class="form-control" value="{{ old('term') }}">
                        @error('term')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">読み方</label>
                        <input type="text" name="reading" class="form-control" value="{{ old('reading') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">意味</label>
                        <textarea name="meaning" class="form-control" rows="2">{{ old('meaning') }}</textarea>
                        @error('meaning')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">メモ</label>
                        <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">新タグ</label>
                        <input type="text" name="new_tag_name" class="form-control" value="{{ old('new_tag_name') }}">
                    </div>

                    <div class="col-12">
                        <div class="tag-checklist">
                            @foreach ($tags as $tag)
                                <label class="form-check tag-checklist__item">
                                    <input class="form-check-input" type="checkbox"
                                           name="tags[]" value="{{ $tag->id }}"
                                           @checked(in_array($tag->id, old('tags', [])))>
                                    <span class="form-check-label">{{ $tag->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary">追加</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 単語一覧 --}}
<ul class="list-group words-list">
@forelse ($words as $word)
<li class="list-group-item">
<div class="row">

    {{-- 単語 --}}
    <div class="col-md-2">
        @if ($word->reading)
            <div class="small text-muted">{{ $word->reading }}</div>
        @endif
        <div class="fw-bold">{{ $word->term }}</div>
    </div>

    {{-- 意味 --}}
    <div class="col-md-4">
        {!! nl2br(e($word->meaning)) !!}
    </div>

    {{-- メモ（広げた） --}}
    <div class="col-md-4">
        @if ($word->note)
            {!! nl2br(e($word->note)) !!}
        @endif
    </div>

    {{-- 操作（タグをここに移動） --}}
    <div class="col-md-2 text-end">

        {{-- タグ --}}
        @foreach ($word->tags as $t)
            <span class="badge text-bg-light border me-1">
                {{ $t->name }}
            </span>
        @endforeach

        <form action="{{ route('wordbooks.words.favorite.toggle', [$wordbook, $word]) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="btn btn-sm {{ $word->is_favorite ? 'btn-warning' : 'btn-outline-secondary' }} mb-1">
                {{ $word->is_favorite ? '★' : '☆' }}
            </button>
        </form>

        <a href="{{ route('wordbooks.words.edit', [$wordbook, $word]) }}"
           class="btn btn-sm btn-outline-primary mb-1">編集</a>

        <form action="{{ route('wordbooks.words.destroy', [$wordbook, $word]) }}" method="POST">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger"
                    onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>

    </div>

</div>
</li>
@empty
<li class="list-group-item text-muted">まだ単語がありません</li>
@endforelse
</ul>
        <div class="mt-3 d-flex justify-content-center">
            {{ $words->links() }}
        </div>

    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const el = document.getElementById('wordbookTabs');
  if (!el) return;

  const url  = el.dataset.reorderUrl;
  const csrf = el.dataset.csrf;

  let suppressClickUntil = 0;
  const suppress = (ms = 300) => { suppressClickUntil = Date.now() + ms; };

  el.addEventListener('click', (e) => {
    const btn = e.target.closest('.wordbook-tab-btn');
    if (!btn) return;

    const item = btn.closest('.wordbook-tab-item');
    if (!item) return;

    if (Date.now() < suppressClickUntil) {
      e.preventDefault();
      e.stopPropagation();
      return;
    }

    window.location.href = item.dataset.href;
  });

  new Sortable(el, {
    animation: 150,
    draggable: '.wordbook-tab-item',
    handle: '.wordbook-tab-btn',
    filter: 'form, button[type="submit"], .add-tab',
    preventOnFilter: true,
    forceFallback: true,
    fallbackOnBody: true,
    touchStartThreshold: 6,

    onStart: () => suppress(500),

    onEnd: async () => {
      suppress(500);

      const ids = Array.from(el.querySelectorAll('.wordbook-tab-item'))
        .map(div => Number(div.dataset.id));

      await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ ids }),
      });
    },
  });
});
</script>
@endpush