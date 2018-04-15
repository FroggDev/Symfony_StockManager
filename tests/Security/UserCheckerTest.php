<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Security;

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
use App\Security\UserChecker;
use App\Tests\Security\FakeClass\UserFake;
use PHPUnit\Framework\TestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserCheckerTest extends TestCase
{

    /*####################
     # UserChecker TESTS #
     ####################*/

    /*
     * test User type in CheckPreAuth method
     */
    public function testCheckPreAuthUserType() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new UserFake();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountTypeException::class);

        $userChecker->checkPreAuth($user);
    }

    /*
     * test User type in CheckPostAuth method
     */
    public function testCheckPostAuthUserType() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new UserFake();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountTypeException::class);

        $userChecker->checkPostAuth($user);
    }


    /**
     * Test for banned user
     */
    public function testBannedUserInUserCheckerClass() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountBannedException::class);

        // test banned exception
        $userChecker->checkPreAuth($user->setBanned());
    }

    /**
     * Test for closed user
     */
    public function testClosedUserInUserCheckerClass() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountClosedException::class);

        //test closed exception
        $userChecker->checkPreAuth($user->setClosed());
    }

    /**
     * Test for deleted user
     */
    public function testDeletedUserInUserCheckerClass() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountDeletedException::class);

        //test deleted exception
        $userChecker->checkPreAuth($user->setDeleted());
    }

    /**
     * Test for disabled user
     */
    public function testDisabledUserInUserCheckerClass() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountDisabledException::class);

        //test disabled exception
        $userChecker->checkPostAuth($user->setDisabled());
    }


    /**
     * Test for active user
     */
    public function testActiveUserInUserCheckerClass() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST (normal case nothing special)
        //-----------------------------------

        $this->assertNull($userChecker->checkPostAuth($user->setEnabled()));

        $this->assertNull($userChecker->checkPreAuth($user->setEnabled()));

        $this->assertNull($userChecker->basicTest($user->setEnabled()));

        $userChecker->checkRegisterValidation($user->setDisabled(),$user->getToken());

        $userChecker->checkRecoverValidation($user->setEnabled()->setToken(),$user->getToken());
    }

    /**
     * Test for checkRegisterValidation
     */
    public function testResisterValidationWhenUserAlreadyEnabled() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountAlreadyActivatedException::class);

        //test disabled exception
        $userChecker->checkRegisterValidation($user->setEnabled(),$user->getToken());
    }


    /**
     * Test for checkRegisterValidation
     */
    public function testResisterValidationWhenBadToken() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountBadTokenException::class);

        //test disabled exception
        $userChecker->checkRegisterValidation($user,'');
    }


    /**
     * Test for checkRecoverValidation
     */
    public function testRecoverValidationWhenExpiredToken() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        $now = new \DateTime();

        $user
            ->setEnabled()
            ->setToken()
            ->testToSetTokenValidity($now->modify('-1 day'));

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountExpiredTokenException::class);

        //test disabled exception
        $userChecker->checkRecoverValidation($user,$user->getToken());
    }
}