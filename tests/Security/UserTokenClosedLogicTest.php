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
class UserTokenClosedLogicTest extends TestCase
{

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
     * Test the User token setter when user is closed
     */
    public function testIfTokenHasBeenRemovedWhenClosed() : void
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

        // Test if date closed is not null
        $this->assertNotNull(
            $user->getDateClosed(),
            'user dateclosed must be not null if user is closed'
        );
    }

}
