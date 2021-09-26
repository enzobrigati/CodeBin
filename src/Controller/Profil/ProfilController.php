<?php

namespace App\Controller\Profil;

use App\Entity\User;
use App\Repository\Paste\PasteRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfilController extends AbstractController
{
    public function __construct(private PaginatorInterface $paginator, private PasteRepository $pasteRepository)
    {
    }

    #[Route(path: '/profil/{pseudo}', name: 'user.profil', requirements: ['pseudo' => '[a-zA-Z0-9_]*'])]
    public function show(User $user, Request $request): RedirectResponse|Response
    {
        $pastes = $this->paginator->paginate(
            $this->pasteRepository->findBy(['privacy' => 'public', 'user' => $user], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            20
        );
        return $this->render('user/profil.html.twig', [
            'pastes' => $pastes,
            'user' => $user,
            'current_page' => $user === $this->getUser() ? 'user_profil' : null
        ]);
    }
}