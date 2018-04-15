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
class UserTokenTest extends TestCase
{
    /**
     * Test the User token setter
     */
    public function testSetToken() : void
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
            'Token must be not null when token is set'
        );

        // Test if token validity is not null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null when token is set to a new user'
        );

        // Test if token is not expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be not exipred when token is set'
        );
    }

    /**
     * Test test If Token Has Been Removed When Activated
     */
    public function testToManualyAddAToken() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // TEST
        //-----

        // Test if token well set
        $this->assertSame(
            $user->setToken('TestToken')->getToken(),
            'TestToken'
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

        // Test if token is not expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after being removed'
        );
    }

    /**
     * Test test If Token Has Been Removed When Activated
     */
    public function testIfTokenIsSetWhenUserIsInactive() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Remove a token
        $user->setDisabled();

        // TEST
        //-----

        // Test if token is not null
        $this->assertNotNull(
            $user->getToken(),
            'Token must be null after account being desactivated'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null after account being desactivated'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred after account being desactivated'
        );
    }
}