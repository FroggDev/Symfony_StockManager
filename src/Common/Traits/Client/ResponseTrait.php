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

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Frogg <admin@frogg.fr>
 */
trait ResponseTrait
{
    /**
     * Remove cache from a response
     * @param Response $response the controller response
     *
     * @return Response
     */
    private function removeCacheFromResponse(Response $response): Response
    {
        $response->setPrivate();
        $response->setMaxAge(0);
        $response->setSharedMaxAge(0);
        $response->headers->addCacheControlDirective('must-revalidate', true);
        $response->headers->addCacheControlDirective('no-store', true);

        return $response;
    }
}
