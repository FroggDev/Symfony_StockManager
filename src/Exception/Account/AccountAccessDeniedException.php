<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Exception\Account;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * @author Frogg <admin@frogg.fr>
 */
class AccountAccessDeniedException extends AccountStatusException
{
}