<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'current_page' => ['index']
        ]);
    }

    #[Route('/charteutilisateur', name: 'main.charteutilisateur')]
    public function charteUtilisateur(): Response
    {
        return $this->render('main/charte.html.twig');
    }

    #[Route('/cgu', name: 'main.cgu')]
    public function Cgu(): Response
    {
        return $this->render('main/cgu.html.twig');
    }

}
