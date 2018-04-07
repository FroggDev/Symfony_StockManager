<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * Handles an access denied failure.
     *
     * @param Request               $request
     * @param AccessDeniedException $accessDeniedException
     *
     * @return Response
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException) : Response
    {
        /**
         * @ TODO : LOG HERE
         * @ TODO TWIG TEMPLATE
         */

        return new Response("TODO A TWIG TEMPLATE ! THIS ERROR HAS BEEN CATCHED BY THE HANDLER (ACCESS DENIED) !");
    }
}
