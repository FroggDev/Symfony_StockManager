<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Security;

use App\Entity\User;
use App\Exception\Account\AccountAlreadyActivatedException;
use App\Exception\Account\AccountBadTokenException;
use App\Exception\Account\AccountBannedException;
use App\Exception\Account\AccountClosedException;
use App\Exception\Account\AccountDeletedException;
use App\Exception\Account\AccountDisabledException;
use App\Exception\Account\AccountExpiredTokenException;
use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\AccountTypeException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * checks before authentication (banned/closed/deleted)
     * @param UserInterface null| $user
     *
     * @throws AccountBannedException
     * @throws AccountClosedException
     * @throws AccountDeletedException
     * @throws AccountTypeException
     */
    public function checkPreAuth(?UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new AccountTypeException('account is unfindable');
        }

        if (true === $user->isBanned()) {
            throw new AccountBannedException('account is banned');
        }

        if (true === $user->isClosed()) {
            throw new AccountClosedException('account is closed');
        }

        if (true === $user->isDeleted()) {
            throw new AccountDeletedException('account is unfindable');
        }

        /*
         * UserChecker Symfony version :
         if (!$user->isEnabled()) {
            $ex = new DisabledException('User account is disabled.');
            $ex->setUser($user);
            throw $ex;
        }
        */
    }

    /**
     *  checks after authentication (disabled mean first check credentials)
     * @param UserInterface $user
     *
     * @throws AccountTypeException
     * @throws AccountDisabledException
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new AccountTypeException('internal error');
        }

        if (true === $user->isDisabled()) {
            throw new AccountDisabledException('account is disabled');
        }
    }

    /**
     * Checks before validate a registration
     * @param null|User   $user
     * @param null|String $token
     *
     * @throws AccountAlreadyActivatedException
     */
    public function checkRegisterValidation(?User $user, ?String $token)
    {
        $this->checkPreAuth($user);

        if (true === $user->isEnabled()) {
            throw new AccountAlreadyActivatedException('account is already activated');
        }

        $this->checkToken($user, $token);
    }


    /**
     * Checks before validate a registration
     * @param null|User   $user
     * @param null|String $token
     *
     * @throws AccountExpiredTokenException
     */
    public function checkRecoverValidation(?User $user, ?String $token)
    {
        $this->checkPreAuth($user);

        $this->checkToken($user, $token);
    }

    /**
     * Check if token is correct
     * @param User        $user
     * @param null|String $token
     *
     * @throws AccountBadTokenException
     */
    private function checkToken(User $user, ?String $token)
    {
        if ($token === null || $token === "" || $user->getToken() !== $token) {
            throw new AccountBadTokenException('account token is not valid');
        }

        if (true === $user->isTokenExpired()) {
            throw new AccountExpiredTokenException('account is expired token');
        }
    }
}
