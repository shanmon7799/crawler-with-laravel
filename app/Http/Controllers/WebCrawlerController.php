<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class WebCrawlerController extends Controller
{
    public function screenshot(Request $request)
    {
        $url = $request->url ?? 'https://blog.techbridge.cc/'; // 要截圖的網站 URL
        Browsershot::url($url)
            ->setOption('landscape', true)
            ->windowSize(1600, 1024)
            ->waitUntilNetworkIdle()
            ->save(public_path() . '/storage/screenshot.png');

        return view('crawler');
    }
}
