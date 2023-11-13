<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{

    /**
     * @return void
     * @test
     */
    public function responseTest(){
        $client=static::createClient();
        $client->request('GET','/');
        $this->assertResponseIsSuccessful();
    }
    /**
     * @return void
     * @test
     */
    public function responseGenreTest(){
        $client=static::createClient();
        $client->request('GET','/genre/28');
        $this->assertResponseIsSuccessful();
    }

    /**
     * @test
     */
    public function getContentPageTest(){
        $client=static::createClient();
        $client->request('GET','/');
        $this->assertSelectorTextContains('h1','Wemovies');
        $this->assertSelectorTextContains('div','Action');
    }

    /**
     * @test
     */
    public function getContentGenrePageTest(){
        $client=static::createClient();
        $client->request('GET','/genre/28');
        $this->assertSelectorTextContains('h1','Wemovies');
        $this->assertSelectorTextContains('div','Action');
    }
}