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
class UserAdvancedTest extends TestCase
{

    /*###############
     # STATUS TESTS #
     ###############*/

    /**
     * Test status
     */
    public function testStatus() : void
    {
        // INIT
        //-----

        // Create the user
        $user = new User();

        // TEST
        //-----

        // test setDisabled
        $this->assertNotTrue(
            $user->setDisabled()->isEnabled(),
            'User must not be enabled after being disabled'
        );
        // test status
        $this->assertEquals($user->getStatus(), $user::DISABLED);


        // test setEnabled
        $this->assertTrue(
            $user->setEnabled()->isEnabled(),
            'User must be enabled after being activated'
        );
        // test status
        $this->assertEquals($user->getStatus(), $user::ENABLED);

        // test setBanned
        $this->assertTrue(
            $user->setBanned()->isBanned(),
            'User must be banned after being banned'
        );
        // test status
        $this->assertEquals($user->getStatus(), $user::BANNED);

        // test setClosed
        $this->assertTrue(
            $user->setClosed()->isClosed(),
            'User must be closed after being closed'
        );
        // test status
        $this->assertEquals($user->getStatus(), $user::CLOSED);

        // test setDeleted
        $this->assertTrue(
            $user->setDeleted()->isDeleted(),
            'User must be deleted after being deleted'
        );
        // test status
        $this->assertEquals($user->getStatus(), $user::DELETED);

    }
}
