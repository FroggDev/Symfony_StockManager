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

use App\Exception\Product\ProductTypeException;
use PHPUnit\Framework\TestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */

class ProductExceptionTest extends TestCase
{
    /** @const string $message */
    const MESSAGE = 'message';

    /*###################
     # Exceptions TESTS #
     ###################*/

    /**
     * Test Product Type Exception
     */
    public function testProductTypeException() : void
    {
        // INIT
        //-----

        $exception = new ProductTypeException($this::MESSAGE);

        // TEST
        //-----

        $this->assertEquals($this::MESSAGE, $exception->getMessageKey());
    }

}
