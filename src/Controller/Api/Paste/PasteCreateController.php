<?php

namespace App\Controller\Api\Paste;

use App\Entity\Paste\Paste;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class PasteCreateController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Paste $data): Paste
    {
        if ($this->security->getUser()) {
            $data->setUser($this->security->getUser());
        }
        return $data;
    }
}