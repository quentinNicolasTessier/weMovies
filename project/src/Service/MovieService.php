<?php

namespace App\Service;

use App\Movie\Movie;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

/**
 *
 */
class MovieService
{
    /**
     * @var HttpClientInterface
     */
    private $client;
    /**
     * @var
     */
    private $apiToken;
    /**
     * @var
     */
    private $apiUrl;

    /**
     * @param $apiToken
     * @param $apiUrl
     * @param HttpClientInterface $client
     */
    public function __construct($apiToken, $apiUrl, HttpClientInterface $client)
    {
        $this->apiToken = $apiToken;
        $this->apiUrl = $apiUrl;
        $this->client = $client;
    }

    /**
     * Permet de creer la requete API
     * @param $url
     * @param $method
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getApiRequest($url, $method)
    {
        return $this->client->request($method, $this->apiUrl . $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Permet de recuperer la liste des films
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getAllMovie()
    {
        $movieByGenreRequest = $this->getApiRequest('discover/movie?language=fr&sort_by=popularity.desc', 'GET');
        return $movieByGenreRequest;
    }

    public function getHomeMovie($movieId)
    {
        $movieHomeRequest = $this->getApiRequest('movie/' . $movieId . '/videos?language=en-US', 'GET');
        return $movieHomeRequest;
    }

    /**
     * Permet de chercher une list de film qui contient $search dans son titre
     * @param $search
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function searchMovie($search)
    {
        $searchMovieRequest = $this->getApiRequest('search/movie?include_adult=false&language=FR&page=1&query=' . $search, 'GET');
        return $searchMovieRequest;
    }

    /**
     * Permet de recuperer la liste des genres de film
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getAllGenre()
    {
        $genresRequest = $this->getApiRequest('genre/movie/list', 'GET');
        return $genresRequest;
    }

    /**
     * Permet de recuperer le film qui à l'id $id
     * @param $movie
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMovieById($movie)
    {
        $detailsMovieRequest = $this->getApiRequest('movie/' . $movie->id, 'GET');
        return $detailsMovieRequest;
    }

    /**
     * Permet de recuperer la list des films qui ont le genre $id
     * @param $id
     * @return \Symfony\Contracts\HttpClient\ResponseInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMovieByGenreRequest($id)
    {
        return $this->getApiRequest('discover/movie?sort_by=popularity.desc&with_genres=' . $id, 'GET');

    }

    /**
     * Permet de recuperer les detail du film ayant l'id $id
     * @param $movies
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getDetailMovie($movies)
    {
        $movieList = [];
        foreach ($movies as $movie) {
            $detailsMovieRequest = $this->getMovieById($movie);
            foreach ($this->client->stream($detailsMovieRequest) as $response => $chunk) {
                if ($chunk->isLast()) {
                    // the full content of $response just completed
                    // $response->getContent() is now a non-blocking call
                    $detailsMovie = json_decode($response->getContent());
                    if ($detailsMovie) {
                        $movie = $this->getMovie($detailsMovie);
                        array_push($movieList, $movie);

                    }

                }
            }
        }
        return $movieList;
    }

    /**
     * Permet de creer une instance de Movie avec les données envoyées par l'api
     * @param $detailsMovie
     * @return Movie
     */
    public function getMovie($detailsMovie)
    {
        $id = $detailsMovie->id;
        $name = $detailsMovie->original_title;
        $description = $detailsMovie->overview;
        $releaseDate = $detailsMovie->release_date;
        $rating = $detailsMovie->vote_average;
        $nbVote = $detailsMovie->vote_count;
        $producers = $detailsMovie->production_companies;
        return new Movie($id, $name, $description, $releaseDate, $rating, $nbVote, $producers);
    }


    /**
     * Permet de recuperer les données de l'api plus vite en utilisant les chunks et les streams
     * @param $request
     * @param $type
     * @return array|mixed
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getStreams($request, $type)
    {
        $list = [];

        foreach ($this->client->stream($request) as $response => $chunk) {
            //Si le chunk à un nouveaux contenu
            if (!$chunk->isLast() && !$chunk->isFirst()) {
                //Recuperation de la reponse api
                $result = json_decode($chunk->getContent());
                if ($type === "movie") {
                    if ($result) {
                        $nbMovie = count($result->results);
                        $firstPartMovie = array_slice($result->results, 0, $nbMovie / 2);
                        $secondPartMovie = array_slice($result->results, $nbMovie / 2, $nbMovie);

                        $firstPartMovie = $this->getDetailMovie($firstPartMovie);
                        $secondPartMovie = $this->getDetailMovie($secondPartMovie);
                        $list = array_merge($firstPartMovie, $secondPartMovie);
                    }
                } elseif ($type === "genre") {
                    $list = $result;
                }
            } elseif ($chunk->isLast()) {
                if ($type === "trailer") {
                    $list = $response->getContent();
                } elseif ($type === "detail") {
                    $detailsMovie = json_decode($response->getContent());
                    if ($detailsMovie) {
                        $movie = $this->getMovie($detailsMovie);
                        array_push($list, $movie);
                    }
                }


            }
        }
        return $list;
    }
}