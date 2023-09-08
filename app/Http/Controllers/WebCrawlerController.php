<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class WebCrawlerController extends Controller
{
    public function index(Request $request, Client $client)
    {
        $url = $request->url ?? 'https://blog.techbridge.cc/'; // input URL
        $this->screenshot($url);
        $crawler = $client->request('GET', $url);
        $baseUri = $crawler->getUri();

        // crawl title
        $title = $this->crawlTitle($crawler);
        // crawl posts and links
        $posts = $this->crawlPosts($crawler);


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
        $this->screenshot($url, '/storage/detail-screenshot.png');
        $crawler = $client->request('GET', $url);

        $title = $this->crawlTitle($crawler);
        $content = $this->crawlContent($crawler);
        $data = [
            'title' => $title,
            'url' => $url,
            'content' => $content,
        ];

        return view('detail', $data);
    }

    public function screenshot($url, $filename = '/storage/screenshot.png') : void
    {
        Browsershot::url($url)
            ->setOption('landscape', true)
            ->windowSize(1600, 1024)
            ->waitUntilNetworkIdle()
            ->save(public_path() . $filename);
    }

    public function crawlTitle($crawler) : string
    {
        return $crawler->filter('title')->text();
    }

    public function crawlPosts($crawler) : array
    {
        $postPreviews = $crawler->filter('.post-preview');
        $result = [];

        foreach ($postPreviews as $postPreview) {
            $postCrawler = new Crawler($postPreview);

            // crawl post title and link adn description
            $post = $postCrawler->filter('h2.post-title')->text();
            $link = $postCrawler->filter('a')->attr('href');
            $description = $crawler->filter('p, post-meta')->text();
            $result[] = [
                'post' => $post,
                'linkUri' => $link,
                'description' => $description
            ];
        }

        return $result;
    }

    public function crawlContent($crawler) : string
    {
        return $crawler->filter('article')->text();
    }
}
