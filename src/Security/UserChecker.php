<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Security;

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
     * @param UserInterface null|$user
     *
     * @throws AccountBannedException
     * @throws AccountClosedException
     * @throws AccountDeletedException
     * @throws AccountTypeException
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new AccountTypeException('internal error');
        }

        if ($user->isBanned()) {
            throw new AccountBannedException('account is banned');
        }

        if ($user->isClosed()) {
            throw new AccountClosedException('account is closed');
        }

        if ($user->isDeleted()) {
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
     *  checks after authentication (disabled)
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

        if (!$user->isEnabled()) {
            throw new AccountDisabledException('account is disabled');
        }
    }

    /**
     * check with basics
     *
     * @param null|User $user
     *
     * @throws AccountNotFoundException
     */
    public function basicTest(?User $user)
    {
        if (!$user) {
            throw new AccountNotFoundException('account is unfindable');
        }

        $this->checkPreAuth($user);
    }


    /**
     * Checks before validate a registration
     * @param User $user
     * @param null|String $token
     *
     * @throws AccountAlreadyActivatedException
     */
    public function checkRegisterValidation(User $user, ?String $token)
    {
        $this->basicTest($user);

        if ($user->isEnabled()) {
            throw new AccountAlreadyActivatedException('account is already activated');
        }

        $this->checkToken($user, $token);
    }


    /**
     * Checks before validate a registration
     * @param User $user
     * @param null|String $token
     *
     * @throws AccountExpiredTokenException
     */
    public function checkRecoverValidation(User $user, ?String $token)
    {
        $this->basicTest($user);

        $this->checkToken($user, $token);

        if ($user->isTokenExpired()) {
            throw new AccountExpiredTokenException('account is expired token');
        }
    }

    /**
     * Check if token is correct
     * @param User $user
     * @param String $token
     *
     * @throws AccountBadTokenException
     */
    private function checkToken(User $user, String $token)
    {
        if ($user->getToken() !== $token) {
            throw new AccountBadTokenException('account token is not valid');
        }
    }
}
