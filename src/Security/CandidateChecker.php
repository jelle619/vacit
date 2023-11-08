<?php

namespace App\Security;

use App\Entity\Candidate as AppUser;
# use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CandidateChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // user account is not verified, the user may be notified
        if ($user->isVerified() === false) {
            throw new CustomUserMessageAccountStatusException('E-mail address not verified.');
        }
    }
}