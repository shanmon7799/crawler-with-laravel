<?php

namespace Tests\Feature;

use App\Http\Controllers\WebCrawlerController;
use App\Service\CrawlerService;
use Goutte\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\DomCrawler\Crawler;
use Tests\TestCase;

class CrawlerTest extends TestCase
{

    public function test_index()
    {
        // prepare Request
        $uri = 'test';
        $baseUri = 'sample.com/';
        $request = $this->createRequest($uri, $baseUri);

        // prepare crawl content
        $html = '<html>
                    <head>
                        <title>Test Title</title>
                    </head>
                    <body>
                        <div class="post-preview">
                            <h2 class="post-title">Post Title 1</h2>
                            <a href="/post/1">Post Link 1</a>
                            <p class="post-meta">Post Description 1</p>
                        </div>
                        <div class="post-preview">
                            <h2 class="post-title">Post Title 2</h2>
                            <a href="/post/2">Post Link 2</a>
                            <p class="post-meta">Post Description 2</p>
                        </div>
                    </body>
                 </html>';
        $crawler = new Crawler($html);

        // use Mockery to mock GuzzleClient
        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')->andReturn($crawler);

        // use Mockery to mock CrawlerService
        $mockPosts = [
            [
                'post' => 'Post Title 1',
                'linkUri' => 'Post Link 1',
                'description' => 'Post Description 1'
            ]
        ];
        $crawlerService = \Mockery::mock(CrawlerService::class);
        $crawlerService->shouldReceive('crawlTitle')->once()->andReturn('Test Title');
        $crawlerService->shouldReceive('crawlPosts')->once()->andReturn($mockPosts);
        $crawlerService->shouldReceive('screenshot')->once();

        // use app instance() dependency injection to mock
        $this->app->instance(CrawlerService::class, $crawlerService);

        // execute
        $response = $this->call('GET', '/crawler', ['request' => $request, 'client' => $client]);

        // assert
        $response->assertStatus(200);
        $response->assertSee('Test Title');
        $response->assertSee('Post Title 1');
    }

    public function testShow()
    {
        // prepare Request
        $uri = 'test';
        $baseUri = 'sample.com/';
        $request = $this->createRequest($uri, $baseUri);

        // prepare crawl content
        $html = '<html>
                    <head>
                        <title>Test Title</title>
                    </head>
                    <body>Test Content</body>
                 </html>';
        $crawler = new Crawler($html);

        // use Mockery to mock GuzzleClient
        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('request')->andReturn($crawler);

        // use Mockery to mock CrawlerService
        $crawlerService = \Mockery::mock(CrawlerService::class);
        $crawlerService->shouldReceive('crawlTitle')->once()->andReturn('Test Title');
        $crawlerService->shouldReceive('crawlContent')->once()->andReturn('Test Content');
        $crawlerService->shouldReceive('screenshot')->once();

        // use app instance() dependency injection to mock
        $this->app->instance(CrawlerService::class, $crawlerService);

        // execute
        $response = $this->call('GET', '/detail', ['request' => $request, 'client' => $client]);

        // assert
        $response->assertStatus(200);
        $response->assertSee('Test Title');
        $response->assertViewHas('content', 'Test Content');
    }

    protected function createRequest($uri, $baseUri)
    {
        return \Illuminate\Http\Request::create('/detail', 'GET', ['uri' => $uri, 'baseUri' => $baseUri]);
    }
}
