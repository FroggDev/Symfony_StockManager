<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception\Product;

use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Throwable;

/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductTypeException extends \Exception
{
    /**
     * @return string
     */
    public function getMessageKey(): string
    {
        return $this->message;
    }
}
