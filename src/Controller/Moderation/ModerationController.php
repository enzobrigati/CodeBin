<?php

namespace App\Controller\Moderation;

use App\Entity\Paste\Paste;
use App\Entity\Paste\Report;
use App\Repository\Paste\PasteRepository;
use App\Repository\Paste\ReportRepository;
use App\Security\Voter\PasteVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/moderation')]
class ModerationController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em, private ReportRepository $reportRepository, private PasteRepository $pasteRepository)
    {
    }

    #[Route(path: '/', name: 'moderation.main')]
    public function index(): Response
    {
        $reports = $this->reportRepository->findAll();
        return $this->render('moderation/index.html.twig', [
            'reports' => $reports,
            'current_page' => ['moderation']
        ]);
    }

    #[Route(path: '/paste/all', name: 'moderation.paste.list')]
    public function listPaste(): Response
    {
        $pastes = $this->pasteRepository->findAll();
        return $this->render('moderation/listpaste.html.twig', [
            'pastes' => $pastes
        ]);
    }

    #[Route(path: '/moderate/{id}', name: 'moderation.report.show', requirements: ['id' => '[0-9]*'])]
    public function show(Report $report): Response
    {
        return $this->render('moderation/showreport.html.twig', [
            'report' => $report
        ]);
    }

    #[Route(path: '/moderate/cancel/{id}', name: 'moderation.report.cancel', requirements: ['id' => '[0-9]*'])]
    public function cancelReport(Report $report): RedirectResponse
    {
        $this->em->remove($report);
        $this->em->flush();
        $this->addFlash('moderation_success', 'Le report a bien été supprimé.');
        return $this->redirectToRoute('moderation.main');
    }

    #[Route(path: '/moderate/delete/{id}', name: 'moderation.paste.delete', requirements: ['id' => '[0-9]*'])]
    public function delete(Paste $paste): RedirectResponse
    {
        $this->denyAccessUnlessGranted(PasteVoter::DELETE, $paste);
        $this->em->remove($paste);
        foreach ($paste->getReports() as $report) {
            $this->em->remove($report);
        }
        $this->em->flush();
        $this->addFlash('moderation_success', 'Le paste a bien été supprimé.');
        return $this->redirectToRoute('moderation.main');
    }
}