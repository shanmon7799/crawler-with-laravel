<?php

namespace App\Http\Controllers;

use App\Service\CrawlerService;
use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class WebCrawlerController extends Controller
{
    private $service;
    public function __construct(CrawlerService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request, Client $client)
    {
        $url = $request->url ?? 'https://blog.techbridge.cc/'; // input URL
        $this->service->screenshot($url);
        $crawler = $client->request('GET', $url);
        $baseUri = $crawler->getUri();

        // crawl title
        $title = $this->service->crawlTitle($crawler);
        // crawl posts and links
        $posts = $this->service->crawlPosts($crawler);


        $data = [
            'baseUri' => $baseUri,
            'title' => $title,
            'posts' => $posts,
        ];

        return view('crawler', $data);
    }

    public function show(Request $request, Client $client)
    {
        $uri = $request->uri;
        $baseUri = $request->baseUri;
        $encodedUri = urlencode($uri); // encode chinese
        $url = $baseUri . $encodedUri;

        $this->service->screenshot($url, '/storage/detail-screenshot.png');

        $crawler = $client->request('GET', $url);
        $title = $this->service->crawlTitle($crawler);
        $content = $this->service->crawlContent($crawler);
        $data = [
            'title' => $title,
            'url' => $url,
            'content' => $content,
        ];

        return view('detail', $data);
    }
}
