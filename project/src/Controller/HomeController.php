<?php

namespace App\Controller;

use App\Form\AnyForm;
use App\Movie\Movie;
use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(HttpClientInterface $client,SerializerInterface $serializer,MovieService $movieService,Request $request): Response
    {

        ini_set('memory_limit', '128M');
        $movieByGenreRequest=$movieService->getAllMovie();
        $form=$this->createForm(AnyForm::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data=$form->getData();
            return $this->redirectToRoute('app_search',['search'=>$data['searchText']]);
        }
        $genresRequest=$movieService->getAllGenre();
        $movieList=$movieService->getStreams($movieByGenreRequest,'movie');
        $resultGenre=$movieService->getStreams($genresRequest,'genre');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'movies'=>$movieList,
            'genres'=>$resultGenre,
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/genre/{id_genre}",name="app_genre")
     */
    public function genre($id_genre,HttpClientInterface $client,MovieService $movieService,Request $request){
        $movieByGenreRequest=$movieService->getMovieByGenreRequest($id_genre);
        $genresRequest=$movieService->getAllGenre();
        $form=$this->createForm(AnyForm::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $data=$form->getData();
            return $this->redirectToRoute('app_search',['search'=>$data['searchText']]);
        }
        $movieList=$movieService->getStreams($movieByGenreRequest,"movie");
        $genres=$movieService->getStreams($genresRequest,"genre");
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'movies'=>$movieList,
            'genres'=>$genres,
            "idGenre"=>$id_genre,
            "form"=>$form->createView()

        ]);
    }

    /**
     * @Route("/search/{search}",name="app_search")
     */
    public function searchAction($search,MovieService $movieService){
        $searchRequest=$movieService->searchMovie($search);
        $movieList=$movieService->getStreams($searchRequest,'movie');
        return $this->render('search/index.html.twig', [
            'controller_name' => 'searchController',
            'movies'=>$movieList,
            'search'=>$search

        ]);
    }
}
