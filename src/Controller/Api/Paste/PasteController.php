<?php

namespace App\Controller\Api\Paste;

use App\Repository\Paste\PasteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PasteController extends AbstractController
{

    private PasteRepository $pasteRepository;

    public function __construct(PasteRepository $pasteRepository){
        $this->pasteRepository = $pasteRepository;
    }

    public function __invoke(): array
    {
        return $this->pasteRepository->findBy(['privacy' => 'public'], ['createdAt' => 'DESC']);
    }

}