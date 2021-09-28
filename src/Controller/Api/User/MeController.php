<?php

namespace App\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class MeController extends AbstractController
{

    public function __construct(private Security $security) {}

    /**
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return $this->json($this->security->getUser());
    }
}