<?php

use App\Movie\Movie;
use App\Service\MovieService;
use PHPUnit\Framework\TestCase ;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieServiceTest extends TestCase
{
    private MovieService $movieService;
    private Movie $movie;
    protected function setUp():void
    {
        $this->movieService=$this->createMock(MovieService::class);
        $this->movie=new Movie(10,'Star Wars Collection',
            "Princess Leia is captured and held hostage by the evil Imperial forces in their effort to take over the galactic Empire. 
            Venturesome Luke Skywalker and dashing captain Han Solo team together with the loveable robot duo R2-D2 and C-3PO to rescue the beautiful 
            princess and restore peace and justice in the Empire","1977-05-25",4.1,19282,[[
            "id"=> 1,
            "logo_path"=> "/o86DbpburjxrqAzEDhXZcyE8pDb.png",
            "name"=> "Lucasfilm Ltd.",
            "origin_country"=> "US"
        ],
        [
            "id"=> 25,
            "logo_path"=> "/qZCc1lty5FzX30aOCVRBLzaVmcp.png",
            "name"=> "20th Century Fox",
            "origin_country"=> "US"
        ]
    ]);
        $this->movieService->method('getDetailMovie')->willReturn([$this->movie]);
        $this->movieService->method('getMovie')->willReturn($this->movie);
        parent::setUp();
    }

    /**
     * @test
     */
    public function getDetailMovieTest(){
        $movieList=$this->movieService->getDetailMovie(1);
       $this->assertIsArray($movieList);
       $this->assertCount(1,$movieList);
       $this->assertContains($this->movie,$movieList);
    }

    /**
     * @test
     */
    public function getMovieTest(){
        $movie=$this->movieService->getMovie([],[]);
        $this->assertEquals($this->movie,$movie);
    }
}