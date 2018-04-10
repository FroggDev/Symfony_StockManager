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
class UserTokenEnabledLogicTest extends TestCase
{
    /**
     * Test the User token setter when user is enabled
     */
    public function testTokenWhenUSerIsEnabled() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set user enabled
        $user->setEnabled();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null for enabled user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must null for enabled user'
        );

        // Test if token is expired
        $this->assertNotTrue(
            $user->isTokenExpired(),
            'Token must not be exipred for enabled user'
        );

        // Test if date closed is null
        $this->assertNull(
            $user->getDateClosed(),
            'user date closed must be null if not banned or closed or deleted'
        );
    }


    /**
     * Test the User token setter when user is enable
     */
    public function testSetTokenAddedAfterBeingEnabled() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Set user enabled
        $user->setEnabled();

        // Add a token
        $user->setToken();

        // TEST
        //-----

        // Test if token is not null
        $this->assertNotNull(
            $user->getToken(),
            'Token must not be null for enabled user'
        );

        // Test if token validity is not null
        $this->assertNotNull(
            $user->getTokenValidity(),
            'Token validity must not be null for enabled user'
        );

        // Test if token validity is valid
        $now = new \DateTime();
        $this->assertTrue(
            $user->getTokenValidity()>new \DateTime(),
            'Token validity must not be higher than now'
        );

        // test if token is expired
        $this->assertTrue(
            $user->getTokenValidity()->modify('-'.(2*$user::TOKENVALIDITYTIME).' day')<new \DateTime(),
            'Token validity must be expired if it is equals as less than now'
        );

        // Test if date closed is null
        $this->assertNull(
            $user->getDateClosed(),
            'user date closed must be null if not banned or closed or deleted'
        );
    }

    /**
     * Test the User token setter when user is enabled
     */
    public function testIfTokenHasBeenRemovedWhenEnabled() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // Add a token
        $user->setToken();

        // Set user Banned
        $user->setEnabled();

        // TEST
        //-----

        // Test if token is null
        $this->assertNull(
            $user->getToken(),
            'Token must be null after for enabled user'
        );

        // Test if token validity is null
        $this->assertNull(
            $user->getTokenValidity(),
            'Token validity must be null for enabled user'
        );

        // Test if date closed is not null
        $this->assertNull(
            $user->getDateClosed(),
            'user dateclosed must be null if user is enabled'
        );
    }
}