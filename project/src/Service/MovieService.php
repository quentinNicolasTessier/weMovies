<?php

namespace App\Service;

use App\Movie\Movie;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieService
{
    private HttpClientInterface $client;
    public function __construct(HttpClientInterface $client){
        $this->client=$client;
    }
    public function getAllMovie(){
        $movieByGenreRequest=$this->client->request('GET','https://api.themoviedb.org/3/discover/movie?language=fr&sort_by=popularity.desc',[
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxOTRkMTU0ZDhlNjFjM2I1NTVhNTcyMTg5MjNjMTg0NyIsInN1YiI6IjY1NGM5MjVhYjE4ZjMyMDBhYzNlY2JjMyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.7PQ7jMZrHppZ6jikIW_YL-zHLg1cC4-PXeM3cuqxEA0',
                'accept' => 'application/json',
            ],
        ]);
        return $movieByGenreRequest;
    }

    public function searchMovie($search){
        $searchMovieRequest=$this->client->request('GET','https://api.themoviedb.org/3/search/movie?include_adult=false&language=FR&page=1&query='.$search,[
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxOTRkMTU0ZDhlNjFjM2I1NTVhNTcyMTg5MjNjMTg0NyIsInN1YiI6IjY1NGM5MjVhYjE4ZjMyMDBhYzNlY2JjMyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.7PQ7jMZrHppZ6jikIW_YL-zHLg1cC4-PXeM3cuqxEA0',
                'accept' => 'application/json',
            ],
        ]);
        return $searchMovieRequest;
    }

    public function getAllGenre(){
        $genresRequest=$this->client->request('GET','https://api.themoviedb.org/3/genre/movie/list',[
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxOTRkMTU0ZDhlNjFjM2I1NTVhNTcyMTg5MjNjMTg0NyIsInN1YiI6IjY1NGM5MjVhYjE4ZjMyMDBhYzNlY2JjMyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.7PQ7jMZrHppZ6jikIW_YL-zHLg1cC4-PXeM3cuqxEA0',
                'accept' => 'application/json',
            ],
        ]);
        return $genresRequest;
    }
    public function getDetailMovie($movies){
        $movieList=[];
        foreach ($movies as $movie){
            $detailsMovieRequest=$this->getMovieById($movie);
            foreach ($this->client->stream($detailsMovieRequest) as $response =>$chunk) {
               if ($chunk->isLast()) {
                    // the full content of $response just completed
                    // $response->getContent() is now a non-blocking call
                   $detailsMovie=json_decode($response->getContent());
                   if($detailsMovie){
                       $collection=$detailsMovie->belongs_to_collection;
                       if($collection){
                           $movie=$this->getMovie($collection,$detailsMovie);
                           array_push($movieList,$movie);
                       }
                   }

                }
            }
        }
        return $movieList;
    }

    public function getMovieById($movie){
        $detailsMovieRequest=$this->client->request('GET','https://api.themoviedb.org/3/movie/'.$movie->id,[
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxOTRkMTU0ZDhlNjFjM2I1NTVhNTcyMTg5MjNjMTg0NyIsInN1YiI6IjY1NGM5MjVhYjE4ZjMyMDBhYzNlY2JjMyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.7PQ7jMZrHppZ6jikIW_YL-zHLg1cC4-PXeM3cuqxEA0',
                'accept' => 'application/json',
            ],
        ]);
        return $detailsMovieRequest;
    }
    public function getMovie($collection,$detailsMovie){
        $id=$collection->id;
        $name=$detailsMovie->original_title;
        $description=$detailsMovie->overview;
        $releaseDate=$detailsMovie->release_date;
        $rating=$detailsMovie->vote_average;
        $nbVote=$detailsMovie->vote_count;
        $producers=$detailsMovie->production_companies;
        return new Movie($id,$name,$description,$releaseDate,$rating,$nbVote,$producers);
    }
    public function getMovieByGenreRequest($id){
        return $this->client->request('GET','https://api.themoviedb.org/3/discover/movie?sort_by=popularity.desc&with_genres='.$id,[
            'headers' => [
                'Authorization' => 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxOTRkMTU0ZDhlNjFjM2I1NTVhNTcyMTg5MjNjMTg0NyIsInN1YiI6IjY1NGM5MjVhYjE4ZjMyMDBhYzNlY2JjMyIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.7PQ7jMZrHppZ6jikIW_YL-zHLg1cC4-PXeM3cuqxEA0',
                'accept' => 'application/json',
            ],
        ]);
    }

    public function getStreams($request,$type){
        $list=[];
        foreach ($this->client->stream($request) as $response =>$chunk) {
            if ($chunk->isLast()) {
            }
            elseif($chunk->isFirst()){

            }
            else{
                $result= json_decode($chunk->getContent());
                if($type==="movie"){
                    if($result){
                        $nbMovie=count($result->results);
                        $firstPartMovie=array_slice($result->results,0,$nbMovie/2);
                        $secondPartMovie=array_slice($result->results,$nbMovie/2,$nbMovie);

                        $firstPartMovie=$this->getDetailMovie($firstPartMovie);
                        $secondPartMovie=$this->getDetailMovie($secondPartMovie);
                        $list=array_merge($firstPartMovie,$secondPartMovie);
                    }
                }
                elseif ($type==="genre"){
                    $list=$result;
                }


            }
        }
        return $list;
    }
}