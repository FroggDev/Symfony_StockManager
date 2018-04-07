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

/**
 * @author Frogg <admin@frogg.fr>
 */
trait BrowserTrait
{
    /**
     * get the user browser language
     *
     * @access private
     *
     * @return string|null
     *
     */
    private function getUserBrowserLangs(): ?string
    {
        preg_match_all('/([a-z]{2})-[A-Z]{2}/', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang);
        if (count($lang) > 0) {
            return $lang[1][0];
        }

        return null;
    }
}
