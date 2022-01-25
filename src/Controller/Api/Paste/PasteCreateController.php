<?php

namespace App\Controller\Api\Paste;

use App\Entity\Paste\Paste;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

#[AsController]
class PasteCreateController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Paste $data): ?Paste
    {
        $data->setUuid($this->generateRandomString(7));
        $data->setUser($this->security->getUser());
        return $data;
    }

    private function generateRandomString($length = 10): string
    {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
    }
}