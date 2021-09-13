<?php

namespace App\Controller\Paste;

use App\Entity\Paste\Paste;
use App\Repository\Paste\PasteRepository;
use App\Security\Voter\PasteVoter;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasteController extends AbstractController
{

    public function __construct(private PasteRepository $pasteRepository, private PaginatorInterface $paginator, private EntityManagerInterface $em)
    {
    }

    #[Route(path: '/pastes', name: 'pastes.list')]
    public function list(Request $request): Response
    {
        $pastes = $this->paginator->paginate(
            $this->pasteRepository->findBy(['privacy' => 'public'], ['createdAt' => 'DESC']),
            $request->query->getInt('page', 1),
            20
        );
        return $this->render('paste/list.html.twig', [
            'current_page' => ['pastes_list'],
            'pastes' => $pastes
        ]);
    }

    #[Route('/paste/{id}', name: 'paste.show', requirements: ['id' => '[0-9]*'])]
    public function show(Paste $paste): Response
    {
        if ($paste->getPrivacy() === 'private' && $paste->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('main');
        }
        return $this->render('paste/index.html.twig', [
            'paste' => $paste
        ]);
    }

    #[Route(path: '/paste/delete/{id}', name: 'paste.delete', requirements: ['id' => '[0-9]*'])]
    public function delete(Paste $paste): RedirectResponse
    {
        $this->denyAccessUnlessGranted(PasteVoter::DELETE, $paste);
        $this->em->remove($paste);
        $this->em->flush();
        $this->addFlash('user_pastes_success', 'Votre paste a bien été supprimé.');
        return $this->redirectToRoute('user.pastes');
    }

}
