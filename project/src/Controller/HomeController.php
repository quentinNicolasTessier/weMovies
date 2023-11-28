<?php

namespace App\Controller;

use App\Form\SearchForm;
use App\Movie\Movie;
use App\Service\MovieService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Permet de recuperer une liste de film et de genre
     * @param MovieService $movieService
     * @param LoggerInterface $logger
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(MovieService $movieService, LoggerInterface $logger): Response
    {

        /**
         * creation du formulaire de recherche
         */
        $form = $this->createForm(SearchForm::class, null, [
            'action' => $this->generateUrl('app_search'),
            'attr' => array(
                'class' => 'form-search'
            )
        ]);

        try {
            //Execution des requetes api de manière asynchrone
            $movieByGenreRequest = $movieService->getAllMovie();
            $homeMovie = $movieService->getHomeMovie("695721");

            //Recuperation de la liste des film envoyée par l'api
            $genresRequest = $movieService->getAllGenre();
            $movieList = $movieService->getStreams($movieByGenreRequest, 'movie');
            //Recuperation de la liste des genres envoyée par l'api
            $resultGenre = $movieService->getStreams($genresRequest, 'genre');

            $trailers = $movieService->getStreams($homeMovie, 'trailer');
        } catch (ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface $e) {
            $logger->info($e->getMessage());
        }
        $trailer = json_decode($trailers)?->results[0]?->key;
        if ($trailer) {
            //appel api pour recuperer le film à la une
            $homeYoutubeUrl = $this->getParameter('app.youtube_url') . $trailer;
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'trailerUrl' => $homeYoutubeUrl,
            'movies' => $movieList,
            'genreList' => $resultGenre,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de recupere une liste de film appartenant au genre $id_genre
     * @param $id_genre
     * @param MovieService $movieService
     * @param LoggerInterface $logger
     * @return Response
     */
    #[Route('/genre/{id_genre}', name: 'app_genre')]
    public function genre($id_genre, MovieService $movieService, LoggerInterface $logger)
    {


        /**
         * Creation du formulaire de recherche de film
         */
        $form = $this->createForm(SearchForm::class, null, [
            'action' => $this->generateUrl('app_search'),
            'attr' => array(
                'class' => 'form-search'
            )
        ]);
        try {
            /**
             * appel aux différentes requete api
             */
            $movieByGenreRequest = $movieService->getMovieByGenreRequest($id_genre);
            $genresRequest = $movieService->getAllGenre();
            $homeMovie = $movieService->getHomeMovie("695721");

            /**
             * Recuperation de données revoyé par l'api via les chunk
             */
            $movieList = $movieService->getStreams($movieByGenreRequest, "movie");
            $genres = $movieService->getStreams($genresRequest, "genre");
            $trailers = $movieService->getStreams($homeMovie, 'trailer');
        } catch (ClientExceptionInterface|TransportExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface $e) {
            $logger->info($e->getMessage());
        }

        //appel à l'api pour recuperer la videos du film à la une
        $homeYoutubeUrl = $this->getParameter('app.youtube_url') . json_decode($trailers)->results[0]->key;

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'trailerUrl' => $homeYoutubeUrl,
            'movies' => $movieList,
            'genreList' => $genres,
            "currentGenre" => $id_genre,
            "form" => $form->createView()

        ]);
    }

    /**
     * Permet de recuperer une liste de film correspondant à la recherche utilisateur
     * @param MovieService $movieService
     * @param RequestStack $request
     * @return Response
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route("/search", name: "app_search")]
    public function searchAction(MovieService $movieService, RequestStack $request)
    {
        //Recuperation des données presents dans $request
        $request = $request->getCurrentRequest()->request->all();

        //Recuperation de la valeur du champs de recherche de film
        $search = $request['search_form']['searchText'];

        //Appel asynchrone vers l'api pour rechercher tout les films correspondant à la recherche
        $searchRequest = $movieService->searchMovie($search);

        //Recuperation des données envoyée par l'api via les chunk
        $movieList = $movieService->getStreams($searchRequest, 'movie');

        return $this->render('search/index.html.twig', [
            'controller_name' => 'searchController',
            'movies' => $movieList,
            'search' => $search

        ]);
    }

    /**
     * Route api pour recuperer le trailer du film
     * @param $movieId
     * @param SerializerInterface $serializer
     * @param MovieService $movieService
     * @return JsonResponse
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     */
    #[Route("/detailMovie/{movieId}", name: "app_detail")]
    public function getDetailMovie($movieId, SerializerInterface $serializer, MovieService $movieService, LoggerInterface $logger): JsonResponse
    {
        //appel de la requete api pour recuperer le trailer
        $homeMovie = $movieService->getHomeMovie($movieId);
        try {
            $trailers = $movieService->getStreams($homeMovie, 'trailer');
        } catch (ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $e) {
            $logger->info($e->getMessage());
        }
        //Recuperation du premier element de la liste de video renvoyé par l'api
        $trailer = json_decode($trailers)?->results[0]?->key;
        if ($trailer) {
            //appel api pour recuperer le film à la une
            $homeYoutubeUrl = $this->getParameter('app.youtube_url') . $trailer;
            $data = $serializer->serialize($homeYoutubeUrl, 'json');

        }
        return new JsonResponse($data, 200, [], true);
    }
}
