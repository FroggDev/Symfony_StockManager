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
use PHPUnit\Framework\TestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserEntityTest extends TestCase
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

        // Test if token is not null
        $this->assertNotNull(
            $user->getToken(),
            'Token must be not null after creating user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after creating user'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after account being desactivated'
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

}