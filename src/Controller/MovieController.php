<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Omdb\OmdbGateway;
use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private OmdbGateway $omdbGateway,
    )
    {
    }

    #[Route('/movies', name: 'app_movie_list')]
    public function list(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render('movie/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/movies/{id}', name: 'app_movie_details')]
    public function details(Movie $movie): Response
    {
        $moviePoster = $this->omdbGateway->getPosterByMovie($movie);

        return $this->render('movie/details.html.twig', [
            'movie' => $movie,
            'moviePoster' => $moviePoster,
        ]);
    }

    #[Route('/movie/create', name: 'app_movie_create')]
    public function create(Request $request, MovieRepository $movieRepository): Response
    {
        $form = $this->createForm(MovieType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $movie = $form->getData();
            $movieRepository->save($movie, true);

            return $this->redirectToRoute('app_movie_list');
        }

        return $this->renderForm('movie/create.html.twig', [
            'creationForm' => $form,
        ]);
    }
}
