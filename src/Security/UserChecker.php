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
use App\Exception\AccountBannedException;
use App\Exception\AccountClosedException;
use App\Exception\AccountDeletedException;
use App\Exception\AccountInactiveException;
use App\Exception\AccountTypeException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserChecker implements UserCheckerInterface
{
    /**
     * checks before authentication (banned/closed/deleted)
     * @param UserInterface $user
     *
     * @throws AccountBannedException
     * @throws AccountClosedException
     * @throws AccountDeletedException
     */
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new AccountTypeException('internal error');
        }

        if($user->isBanned()){
            throw new AccountBannedException('account is banned');
        }

        if($user->isClosed()){
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
     * @throws AccountInactiveException
     */
    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new AccountTypeException('internal error');
        }

        if(!$user->isEnabled()){
            throw new AccountInactiveException('account is disabled');
        }
    }
}
