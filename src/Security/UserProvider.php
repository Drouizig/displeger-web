<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword('$2y$13$W6Q.FFoPZmYZjDC/IBA8qebUVoM0Famj49jBuV6quRRNuLPs/v/4O');
        $user->setRoles(['ROLE_ADMIN']);

        return $user;
    }

    /**
     * Refreshes the user after being reloaded from the session.
     *
     * When a user is logged in, at the beginning of each request, the
     * User object is loaded from the session and then this method is
     * called. Your job is to make sure the user's data is still fresh by,
     * for example, re-querying for fresh User data.
     *
     * If your firewall is "stateless: true" (for a pure API), this
     * method is not called.
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $user = new User();
        $user->setUsername('admin');
        $user->setPassword('$2y$13$W6Q.FFoPZmYZjDC/IBA8qebUVoM0Famj49jBuV6quRRNuLPs/v/4O');
        $user->setRoles(['ROLE_ADMIN']);
        return $user;
    }

    /**
     * Tells Symfony to use this provider for this User class.
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
