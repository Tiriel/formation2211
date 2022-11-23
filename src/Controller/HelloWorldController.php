<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController extends AbstractController
{
    #[Route('/hello/{name}', name: 'app_hello_world')]
    public function __invoke(Request $request, string $name = 'World'): Response
    {
        return $this->render('hello_world/index.html.twig', [
            'controller_name' => $name,
        ]);
    }
}


