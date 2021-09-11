<?php

namespace App\Controller\Api\User;

use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsController]
class MeController
{

    public function __construct(private Security $security) {}

    /**
     * @return UserInterface|null
     */
    public function __invoke(): ?UserInterface
    {
        return $this->security->getUser();
    }
}