<?php

namespace App\Controller\Paste;

use App\Entity\Paste\Paste;
use App\Security\Voter\PasteVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route(path: '/paste')]
class PasteController extends AbstractController
{
    #[Route('/{id}', name: 'paste.show', requirements: ['id' => '[0-9]*'])]
    public function show(Paste $paste): Response
    {
        $this->denyAccessUnlessGranted(PasteVoter::READ, $paste);
        return $this->render('paste/index.html.twig', [
            'paste' => $paste
        ]);
    }

}
