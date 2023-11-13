<?php

use App\Movie\Movie;

class MovieTest extends \PHPUnit\Framework\TestCase
{
    private $movie;
    private $movie2;
    protected function setUp():void
    {
        $this->movie=new Movie(12,'testMovie','description movie',"2023-11-10",2.5,1000,[]);
        $this->movie2=new Movie(21,'testMovie2','description movie2',"2023-10-11",5.0,10,[["id"=>10,"name"=>"Universal"]]);

        parent::setUp();
    }
    /**
     * @test
     */
    public function getMovieTest(){
        $this->assertNotNull($this->movie);
    }

    /**
     * @return void
     * @test
     */
    public function getIdTest(){
        $id=$this->movie->getId();
        $this->assertEquals(12,$id);
        $this->assertIsInt($id);
    }

    /**
     * @return void
     * @test
     */
    public function setIdTest(){
        $this->movie->setId($this->movie2->getId());
        $this->assertEquals(21,$this->movie->getId());
        $this->assertIsInt($this->movie->getId());
    }

    /**
     * @test
     */
    public function getTitleTest(){
        $title=$this->movie->getTitle();
        $this->assertEquals("testMovie",$title);
        $this->assertIsString($title);
    }

    /**
     * @return void
     * @test
     */
    public function setTitleTest(){
        $this->movie->setTitle($this->movie2->getTitle());
        $this->assertEquals("testMovie2",$this->movie->getTitle());
        $this->assertIsString($this->movie->getTitle());
    }

    /**
     * @test
     */
    public function getDescriptionTest(){
        $description=$this->movie->getDescription();
        $this->assertEquals("description movie",$description);
        $this->assertIsString($description);
    }

    /**
     * @return void
     * @test
     */
    public function setDescriptionTest(){
        $this->movie->setDescription($this->movie2->getDescription());
        $this->assertEquals("description movie2",$this->movie->getDescription());
        $this->assertIsString($this->movie->getDescription());
    }

    /**
     * @test
     */
    public function getReleaseDateTest(){
        $releaseDate=$this->movie->getReleaseDate();
        $this->assertEquals("2023-11-10",$releaseDate);
        $this->assertIsString($releaseDate);
    }

    /**
     * @return void
     * @test
     */
    public function setReleaseDateTest(){
        $this->movie->setReleaseDate($this->movie2->getReleaseDate());
        $this->assertEquals("2023-10-11",$this->movie->getReleaseDate());
        $this->assertIsString($this->movie->getReleaseDate());
    }

    /**
     * @test
     */
    public function getRatingTest(){
        $rating=$this->movie->getRating();
        $this->assertEquals(2.5,$rating);
        $this->assertIsFloat($rating);
    }

    /**
     * @test
     */
    public function setRatingTest(){
        $this->movie->setRating($this->movie2->getRating());
        $this->assertEquals(5,$this->movie->getRating());
        $this->assertIsFloat($this->movie->getRating());
    }

    /**
     * @test
     */
    public function getNumberVoteTest(){
        $nbVote=$this->movie->getVoteNumber();
        $this->assertEquals(1000,$nbVote);
        $this->assertIsInt($nbVote);
    }

    /**
     * @test
     */
    public function setNumberVoteTest(){
        $this->movie->setVoteNumber($this->movie2->getVoteNumber());
        $this->assertEquals(10,$this->movie->getVoteNumber());
        $this->assertIsInt($this->movie->getVoteNumber());
    }

    /**
     * @test
     */
    public function getProducersTest(){
        $producers=$this->movie->getProducers();
        $this->assertEquals([],$producers);
        $this->assertIsArray($producers);
        $this->assertEmpty($producers);
    }

    /**
     * @test
     */
    public function setProducersTest(){
        $producers=$this->movie2->getProducers();
        $this->movie->setProducers($producers);
        $this->assertEquals([["id"=>10,"name"=>"Universal"]],$this->movie->getProducers());
        $this->assertIsArray($this->movie->getProducers());
        $this->assertNotEmpty($this->movie->getProducers());
    }

}