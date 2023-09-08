<!DOCTYPE html>
<html>
<head>
    <title>網頁爬蟲結果</title>
</head>
<body>
<h1>網頁爬蟲結果</h1>
<h2>網頁截圖：</h2>
<img src="{{ asset('storage/detail-screenshot.png') }}" alt="screenshot">

<h2>網頁標題：</h2>
<a href="{{ $url }}">
    <h3 class="title">
        {{ $title }}
    </h3>
</a>

<h2>文章內容：</h2>
    <div class="container">
        <p>
            {{ $content }}
        </p>
    </div>
</body>
</html>
