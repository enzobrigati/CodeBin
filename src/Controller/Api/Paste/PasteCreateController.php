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

    public function __invoke(Paste $data): Paste
    {
        $uuid = Uuid::v4();
        $data->setUuid($uuid);
        if ($this->security->getUser()) {
            $data->setUser($this->security->getUser());
        }
        if($data->getPrivacy() === 'private' && !$this->security->getUser()) {
            $data->setPrivacy('unlisted');
        }
        return $data;
    }
}