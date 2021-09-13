<?php

namespace App\Controller\Api\Paste;

use App\Repository\Paste\PasteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class UserGetPastesController extends AbstractController
{

    private PasteRepository $pasteRepository;
    private Security $security;

    public function __construct(PasteRepository $pasteRepository, Security $security){
        $this->pasteRepository = $pasteRepository;
        $this->security = $security;
    }

    public function __invoke(): array
    {
        return $this->pasteRepository->findBy(['user' => $this->security->getUser()]);
    }

}