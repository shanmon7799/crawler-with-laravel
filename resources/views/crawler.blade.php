<!DOCTYPE html>
<html>
<head>
    <title>網頁爬蟲結果</title>
</head>
<body>
<h1>網頁爬蟲結果</h1>
<h2>網頁截圖：</h2>
<img src="{{ asset('storage/screenshot.png') }}" alt="screenshot">

<h2>網頁標題：</h2>
<a href="{{ $baseUri }}">
    <h3 class="title">
        {{ $title }}
    </h3>
</a>

<h2>文章標題：</h2>
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-md-10 mx-auto">
            @foreach ($posts as $post)
                <div class="post-preview">
                    <a href="{{ $baseUri . $post['linkUri'] }}">
                        <h3 class="post-title">
                            {{ $post['post'] }}
                        </h3>
                    </a>
                    <a href="{{ url('/detail') . '?baseUri=' . $baseUri . '&uri=' . $post['linkUri'] }}">
                        <h4 class="post-detail">
                            Detail
                        </h4>
                    </a>
                    <p class="post-description">
                        {{$post['description']}}
                    </p>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
</div>

</body>
</html>
