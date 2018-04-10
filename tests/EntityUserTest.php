<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests;

use App\Entity\User;
use App\Exception\AccountBannedException;
use App\Exception\AccountClosedException;
use App\Exception\AccountDeletedException;
use App\Exception\AccountInactiveException;
use App\Exception\AccountTypeException;
use App\Security\UserChecker;
use App\Tests\FakeClass\UserFake;
use PHPUnit\Framework\TestCase;


/**
 * @author Frogg <admin@frogg.fr>
 */
class EntityUserTest extends TestCase
{

    /*#############
     # MAIN TESTS #
     #############*/

    /**
     * Test the User contructor
     */
    public function testUserContructor(): void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // TEST
        //-----

        // Test the Type return by the constructor
        $this->assertInstanceOf(
            User::class,
            $user,
            'New User must be instance of User'
        );

        // Test if user has ROLE_MEMBER
        $this->assertTrue(
            $user->hasRole('ROLE_MEMBER'),
            'New user must have the role ROLE_MEMBER'
        );

        // Test if user only 1 role (ROLE_MEMBER)
        $this->assertCount(
            1,
            $user->getRoles(),
            'New user should have only 1 role (ROLE_MEMBER)'
        );

        // Test if user is disabled
        $this->assertNotTrue(
            $user->isEnabled(),
            'New user must be disabled to validate his account by mail'
        );
    }

    /*######################
     # GETTER/SETTER TESTS #
     ######################*/


    public function testGetterAndSetter() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();


        // TEST
        //-----

        // getSalt
        $this->assertNull(
            $user->getSalt(),
            'User getSalt() must return null'
        );

        //ID
        $this->assertEquals(1,
            $user->setId(1)->getId()
        );

        //FIRSTNAME
        $this->assertEquals('First Name',
            $user->setFirstname('First Name')->getFirstName()
        );

        //LASTNAME
        $this->assertEquals('Last Name',
            $user->setLastName('Last Name')->getLastName()
        );

        //EMAIL
        $this->assertEquals('email@frogg.fr',
            $user->setEmail('email@frogg.fr')->getEmail()
        );

        //PASSWORD
        $this->assertEquals('Password',
            $user->setPassword('Password')->getPassword()
        );

        //DATE INSCRIPTION
        $now = new \DateTime();
        $this->assertEquals($now,
            $user->setDateInscription($now)->getDateInscription()
        );

        //LAST CONNEXION
        $this->assertNotNull($user->setLastConnexion()->getLastConnexion());

        //GETUSERNAME
        $this->assertEquals('email@frogg.fr',
            $user->setEmail('email@frogg.fr')->getUsername()
        );

    }

    /*###############
     # STATUS TESTS #
     ###############*/

    public function testStatus() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // TEST
        //-----

        // Set user Inactive
        $this->assertNotTrue(
            $user->setInactive()->isEnabled(),
            'User must not be enabled after being disabled'
        );
        $this->assertEquals($user->getStatus(), $user::INACTIVE);


        // Set user Active
        $this->assertTrue(
            $user->setActive()->isEnabled(),
            'User must be enabled after being activated'
        );
        $this->assertEquals($user->getStatus(), $user::ACTIVE);

        // Set user Banned
        $this->assertTrue(
            $user->setBanned()->isBanned(),
            'User must be banned after being banned'
        );
        $this->assertEquals($user->getStatus(), $user::BANNED);

        // Set user Closed
        $this->assertTrue(
            $user->setClosed()->isClosed(),
            'User must be closed after being closed'
        );
        $this->assertEquals($user->getStatus(), $user::CLOSED);

    }


    /*##############
     # TOKEN TESTS #
     ##############*/

    /**
     * Test the User token setter
     */
    public function testSetToken(): void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // TEST
        //-----

        // Test if token is not null
        $this->assertNotNull(
            $user->getToken(),
            'Token must not be null after being created'
        );

        // Test if token validity is not null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after being created'
        );

        // Test if date closed is null
        $this->assertNull(
            $user->getDateClosed(),
            'user date closed must be null if not banned or closed'
        );
    }

    /**
     * Test the User token setter when user is active
     */
    public function testSetTokenAddedAfterBeingActive() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set user Active
        $user->setActive();

        // Add a token
        $user->setToken();

        // TEST
        //-----

        // Test if token is not null
        $this->assertNotNull(
            $user->getToken(),
            'Token must not be null for active user'
        );

        // Test if token validity is not null
        $this->assertNotNull(
            $user->getTokenValidity(),
            'Token validity must not be null for active user'
        );

        // Test if token validity is valid
        $now = new \DateTime();
        $this->assertTrue(
            $now->diff($user->getTokenValidity())->days < User::TOKENVALIDITYTIME,
            'Token validity must not be lower than TOKENVALIDITYTIME'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred for active user'
        );

        // Test if date closed is null
        $this->assertNull(
            $user->getDateClosed(),
            'user date closed must be null if not banned or closed'
        );
    }

    /**
     * Test the User token setter when user is banned
     */
    public function testSetTokenAddedAfterBeingBanned() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set user Banned
        $user->setBanned();

        // Add a token
        $user->setToken();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null for banned user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for banned user'
        );
    }

    /**
     * Test the User token setter when user is closed
     */
    public function testSetTokenAddedAfterBeingClosed() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set user Closed
        $user->setClosed();

        // Add a token
        $user->setToken();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null for closed user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for closed user'
        );
    }


    /**
     * Test the User token setter when user is closed
     */
    public function testSetTokenAddedAfterBeingDeleted() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set user Deleted
        $user->setDeleted();

        // Add a token
        $user->setToken();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null for deleted user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for deleted user'
        );
    }

    /**
     * Test the User token setter when user is actived
     */
    public function testSetTokenActivated() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Set user Banned
        $user->setActive();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after for activated user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for activated user'
        );

        // Test if date closed is not null
        $this->assertNull(
            $user->getDateClosed(),
            'user dateclosed must be null if user is activated'
        );
    }

    /**
     * Test the User token setter when user is banned
     */
    public function testSetTokenBanned() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Set user Banned
        $user->setBanned();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after for banned user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for banned user'
        );

        // Test if date closed is not null
        $this->assertNotNull(
            $user->getDateClosed(),
            'user dateclosed must not be null if user is banned'
        );
    }

    /**
     * Test the User token setter when user is closed
     */
    public function testSetTokenClosed() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Set user Closed
        $user->setClosed();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after for closed user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for closed user'
        );

        // Test if date closed is not null
        $this->assertNotNull(
            $user->getDateClosed(),
            'user dateclosed must not be null if user is closed'
        );
    }

    /**
     * Test the User token setter when user is deleted
     */
    public function testSetTokenDeleted() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();


        // Add a token
        $user->setToken();


        // Set user Deleted
        $user->setDeleted();


        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after for deleted user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for deleted user'
        );

        // Test if date closed is not null
        $this->assertNotNull(
            $user->getDateClosed(),
            'user dateclosed must not be null if user is closed'
        );
    }
    /**
     * Test the User token remover
     */
    public function testRemoveToken() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Remove a token
        $user->removeToken();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after being removed'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after being removed'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after being removed'
        );
    }

    /**
     * Test test If Token Has Been Removed When Activated
     */
    public function testIfTokenHasBeenRemovedWhenActivated() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Remove a token
        $user->setActive();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after account being activated'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after account being activated'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after account being activated'
        );
    }

    /**
     * Test test If Token Has Been Removed When Deleted
     */
    public function testIfTokenHasBeenRemovedWhenDeleted() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Remove a token
        $user->setDeleted();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after account being deleted'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after account being deleted'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after account being deleted'
        );
    }

    /**
     * Test test If Token Has Been Removed When Closed
     */
    public function testIfTokenHasBeenRemovedWhenClosed() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Remove a token
        $user->setClosed();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after account being closed'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after account being closed'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after account being closed'
        );
    }


    /**
     * Test test If Token Has Been Removed When Banend
     */
    public function testIfTokenHasBeenRemovedWhenbanned() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Remove a token
        $user->setBanned();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after account being banned'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after account being banned'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after account being banned'
        );
    }

    /*##############
     # OTHER TESTS #
     ##############*/

    /**
     * Test method testEraseCredentials
     */
    public function testEraseCredentials() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // TEST
        //-----
        $this->assertNull($user->eraseCredentials());
    }

    /**
     * Test method setAllRoles
     */
    public function testSetAllRoles() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set roles
        $user->setAllRoles(['ROLE_TEST1', 'ROLE_TEST2']);

        // TEST
        //-----

        // Test if user has ROLE_TEST1
        $this->assertTrue($user->hasRole('ROLE_TEST1'));

        // Test if user has ROLE_TEST1
        $this->assertTrue($user->hasRole('ROLE_TEST2'));

        // Test if user only 1 role (ROLE_MEMBER)
        $this->assertCount(2, $user->getRoles());
    }


    /*####################
     # UserChecker TESTS #
     ####################*/

    public function testCheckPreAuthUserType()
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

    public function testCheckPostAuthUserType()
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

        $userChecker->checkPreAuth($user->setDeleted());
    }

    /**
     * Test for inactive user
     */
    public function testInactiveUserInUserCheckerClass() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Create a new user checker
        $userChecker = new UserChecker();

        // TEST
        //-----

        $this->expectException(AccountInactiveException::class);

        $userChecker->checkPostAuth($user->setInactive());
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

        // TEST
        //-----

        $this->assertNull($userChecker->checkPostAuth($user->setActive()));

        $this->assertNull($userChecker->checkPreAuth($user->setActive()));
    }

    /*###################
     # Exceptions TESTS #
     ###################*/

    /**
     * Test Account Banned Exception
     */
    public function testAccountBannedException() : void
    {
        // INIT
        //-----

        $exception = new AccountBannedException('message');

        // TEST
        //-----

        $this->assertEquals('message', $exception->getMessageKey());
    }

    /**
     * Test Account Closed Exception
     */
    public function testAccountClosedException() : void
    {
        // INIT
        //-----

        $exception = new AccountClosedException('message');

        // TEST
        //-----

        $this->assertEquals('message', $exception->getMessageKey());
    }

    /**
     * Test Account Deleted Exception
     */
    public function testAccountDeletedException() : void
    {
        // INIT
        //-----

        $exception = new AccountDeletedException('message');

        // TEST
        //-----

        $this->assertEquals('message', $exception->getMessageKey());
    }

    /**
     * Test Account Inactive Exception
     */
    public function testAccountInactiveException() : void
    {
        // INIT
        //-----

        $exception = new AccountInactiveException('message');

        // TEST
        //-----

        $this->assertEquals('message', $exception->getMessageKey());
    }

    /**
     * Test Account Type Exception
     */
    public function testAccountTypeException() : void
    {
        // INIT
        //-----

        $exception = new AccountTypeException('message');

        // TEST
        //-----

        $this->assertEquals('message', $exception->getMessageKey());
    }
}
