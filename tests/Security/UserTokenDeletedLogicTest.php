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
class UserTokenDeletedLogicTest extends TestCase
{

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
            'user dateclosed must not be null if user is deleted'
        );
    }

    /**
     * Test the User token setter when user is closed
     */
    public function testIfTokenHasBeenRemovedWhenDeleted() : void
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

        // Test if date closed is not null
        $this->assertNotNull(
            $user->getDateClosed(),
            'user dateclosed must be not null if user is deleted'
        );

    }
}