<?php

namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class WebCrawlerController extends Controller
{
    public function index(Request $request)
    {
        $url = $request->url ?? 'https://blog.techbridge.cc/'; // input URL
        $this->screenshot($url);
        $client = new Client();
        $crawler = $client->request('GET', $url);
        $baseUri = $crawler->getUri();

        // crawl title
        $title = $this->crawlTitle($crawler);
        // crawl posts and links
        $posts = $this->crawlPosts($crawler, $baseUri);


        $data = [
            'baseUri' => $baseUri,
            'title' => $title,
            'posts' => $posts,
        ];

        return view('crawler', $data);
    }

    public function screenshot($url) : void
    {
        Browsershot::url($url)
            ->setOption('landscape', true)
            ->windowSize(1600, 1024)
            ->waitUntilNetworkIdle()
            ->save(public_path() . '/storage/screenshot.png');
    }

    public function crawlTitle($crawler) : string
    {
        return $crawler->filter('title')->text();
    }

    public function crawlPosts($crawler, $baseUri) : array
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
                'link' => $baseUri . $link,
                'description' => $description
            ];
        }

        return $result;
    }
}
