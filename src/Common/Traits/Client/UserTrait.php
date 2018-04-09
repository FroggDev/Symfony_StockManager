<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Common\Traits\Client;

use App\SiteConfig;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * @author Frogg <admin@frogg.fr>
 */
trait UserTrait
{

    /**
     * Create and return a user cookie to store his email
     * @param string $email
     *
     * @return Cookie
     */
    private function getUserCookie(string $email) : Cookie
    {
        return new Cookie(
            SiteConfig::COOKIEUSERNAME,
            $email,
            // 24 * 60 * 60 = 86400 = 1 day
            time() + (SiteConfig::COOKIEUSERVALIDITY * 24 * 60 * 60)
        );
    }
}
