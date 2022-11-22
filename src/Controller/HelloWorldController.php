<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    /*

    /hello?name=Adrien
    /hello/Adrien (/hello doit continuer à fonctionner)
    */
    #[Route('/hello', name: 'app_hello_world')]
    public function index(Request $request): Response
    {
        $name = $request->query->get('name') ?? 'World';

        return $this->render('hello_world/index.html.twig', [
            'controller_name' => $name,
        ]);
    }

    #[Route('/loremipsum', name: 'app_loremipsum')]
    public function loremipsum(Request $request): Response
    {
        dump($request->attributes);
        $name = $request->query->get('name') ?? 'World';

        return $this->render('hello_world/index.html.twig', [
            'controller_name' => $name,
        ]);
    }

}
