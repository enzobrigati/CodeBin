<?php

namespace App\Security\Voter;

use App\Entity\Paste\Paste;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class PasteVoter extends Voter
{

    public function __construct(private Security $security)
    {
    }

    const READ = 'CAN_READ_PASTE';
    const EDIT = 'CAN_EDIT_PASTE';
    const DELETE = 'CAN_DELETE_PASTE';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::READ, self::EDIT, self::DELETE])
            && $subject instanceof Paste;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        /** @var Paste $subject */

        switch ($attribute) {
            case self::READ:
                if ($this->security->isGranted('ROLE_MODERATOR')) {
                    return true;
                }
                if ($subject->getPrivacy() === 'private' && $subject->getUser() === $user) {
                    return true;
                } elseif ($subject->getPrivacy() !== 'private') {
                    return true;
                }
                break;
            case self::EDIT:
            case self::DELETE:
                if ($this->security->isGranted('ROLE_MODERATOR')) {
                    return true;
                }
                return $subject->getUser() === $user;
        }

        return false;
    }
}
