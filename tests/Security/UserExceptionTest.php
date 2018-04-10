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

use App\Exception\Account\AccountAlreadyActivatedException;
use App\Exception\Account\AccountBadTokenException;
use App\Exception\Account\AccountBannedException;
use App\Exception\Account\AccountClosedException;
use App\Exception\Account\AccountDeletedException;
use App\Exception\Account\AccountDisabledException;
use App\Exception\Account\AccountExpiredTokenException;
use App\Exception\Account\AccountNotFoundException;
use App\Exception\Account\AccountTypeException;
use PHPUnit\Framework\TestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */

class UserExceptionTest extends TestCase
{
    /** @const string $message */
    const MESSAGE = 'message';

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

        $exception = new AccountBannedException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Closed Exception
     */
    public function testAccountClosedException() : void
    {
        // INIT
        //-----

        $exception = new AccountClosedException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Deleted Exception
     */
    public function testAccountDeletedException() : void
    {
        // INIT
        //-----

        $exception = new AccountDeletedException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Disabled Exception
     */
    public function testAccountDisabledException() : void
    {
        // INIT
        //-----

        $exception = new AccountDisabledException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Type Exception
     */
    public function testAccountTypeException() : void
    {
        // INIT
        //-----

        $exception = new AccountTypeException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Already Activated Exception
     */
    public function testAccountAlreadyActivatedException() : void
    {
        // INIT
        //-----

        $exception = new AccountAlreadyActivatedException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Bad Token Exception
     */
    public function testAccountBadTokenException() : void
    {
        // INIT
        //-----

        $exception = new AccountBadTokenException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Expired Token Exception
     */
    public function testAccountExpiredTokenException() : void
    {
        // INIT
        //-----

        $exception = new AccountExpiredTokenException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

    /**
     * Test Account Not Found Exception
     */
    public function testAccountNotFoundException() : void
    {
        // INIT
        //-----

        $exception = new AccountNotFoundException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }
}
