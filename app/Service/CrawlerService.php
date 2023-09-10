<?php

namespace App\Service;

use Spatie\Browsershot\Browsershot;
use Symfony\Component\DomCrawler\Crawler;

class CrawlerService
{
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
