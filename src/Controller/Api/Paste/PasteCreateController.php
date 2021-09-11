<?php

namespace App\Controller\Api\Paste;

use App\Entity\Paste\Paste;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class PasteCreateController extends AbstractController
{

    public function __construct(private Security $security){}

    public function __invoke(Paste $data): Paste
    {
        if($this->security->getUser()) {
            $data->setUser($this->security->getUser());
        }
        return $data;
    }

}