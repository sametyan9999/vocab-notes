<!doctype html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>単語帳</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- ページごとのCSS --}}
    @stack('styles')
</head>
<body class="app-bg">
<div class="container py-4">

    <h1 class="notebook-title">📒 My単語帳</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @yield('content')

</div>

{{-- ページごとのJS（Sortable含む） --}}
@stack('scripts')

{{-- ★ これを追加（超重要） --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>