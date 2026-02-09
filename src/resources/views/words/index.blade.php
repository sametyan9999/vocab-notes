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

        {{-- ＋ 新しい単語帳 --}}
        <form method="POST" action="{{ route('wordbooks.store') }}" class="d-inline">
            @csrf
            <input type="hidden" name="name" value="">
            <button type="submit" class="wordbook-tab add-tab">＋</button>
        </form>
    </div>

    {{-- =======================
         📄 ノート本文
    ======================== --}}
    <div class="notebook-page">

        <div class="mb-3 d-flex gap-2 flex-wrap align-items-center">

            <form method="POST" action="{{ route('wordbooks.destroy', $wordbook) }}"
                  onsubmit="return confirm('この単語帳を削除しますか？（中の単語もすべて削除され、元に戻せません。）')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    この単語帳を削除
                </button>
            </form>

            <form method="POST" action="{{ route('wordbooks.update', $wordbook) }}"
                  class="d-flex gap-2 align-items-center">
                @csrf
                @method('PATCH')
                <input type="text" name="name"
                       class="form-control form-control-sm"
                       style="width: 220px;"
                       value="{{ old('name', $wordbook->name) }}">
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    名前変更
                </button>
            </form>

            <form method="GET" action="{{ route('wordbooks.words.index', $wordbook) }}"
                  class="ms-auto d-flex align-items-center gap-1">
                <input type="hidden" name="q" value="{{ $q }}">
                <input type="hidden" name="tag" value="{{ $tagId }}">

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
        </div>

        @if ($errors->has('name'))
            <div class="alert alert-danger py-2">
                {{ $errors->first('name') }}
            </div>
        @endif

        {{-- タイトル＋検索 --}}
        <div class="words-toolbar d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="words-toolbar__left d-flex align-items-center gap-2">
                <h2 class="m-0">単語一覧</h2>
                <a href="{{ route('wordbooks.tags.index', $wordbook) }}"
                   class="btn btn-sm btn-outline-secondary">
                    🏷 タグ管理
                </a>
            </div>

            <form method="GET" action="{{ route('wordbooks.words.index', $wordbook) }}"
                  class="d-flex align-items-center gap-2 flex-wrap">
                <input type="text" name="q" value="{{ $q }}"
                       class="form-control form-control-sm"
                       style="width: 220px;"
                       placeholder="検索">

                <select name="tag" class="form-select form-select-sm" style="width: 180px;">
                    <option value="">タグ</option>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->id }}" @selected((string)$tagId === (string)$tag->id)>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>

                <input type="hidden" name="sort" value="{{ $sort }}">
                <button type="submit" class="btn btn-sm btn-secondary">検索</button>
                <a href="{{ route('wordbooks.words.index', $wordbook) }}"
                   class="btn btn-sm btn-outline-secondary">クリア</a>
            </form>
        </div>

        {{-- 単語追加フォーム --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" action="{{ route('wordbooks.words.store', $wordbook) }}"
                      class="row g-2 align-items-start">
                    @csrf
                    <div class="col-md-2">
                        <label class="form-label small mb-1">単語</label>
                        <input type="text" name="term" class="form-control" value="{{ old('term') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">読み方</label>
                        <input type="text" name="reading" class="form-control" value="{{ old('reading') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">意味</label>
                        <textarea name="meaning" class="form-control" rows="2" required>{{ old('meaning') }}</textarea>
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
                        <div class="col-md-2">
                            @if ($word->reading)
                                <div class="small text-muted">{{ $word->reading }}</div>
                            @endif
                            <div class="fw-bold">{{ $word->term }}</div>
                        </div>
                        <div class="col-md-4">{!! nl2br(e($word->meaning)) !!}</div>
                        <div class="col-md-2">
                            @if ($word->note)
                                📝 {!! nl2br(e($word->note)) !!}
                            @endif
                        </div>
                        <div class="col-md-2">
                            @foreach ($word->tags as $t)
                                <span class="badge text-bg-light border">{{ $t->name }}</span>
                            @endforeach
                        </div>
                        <div class="col-md-2 text-end">
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