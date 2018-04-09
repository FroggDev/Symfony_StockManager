<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Common\Traits\Client\ResponseTrait;
use App\Service\LocaleManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Frogg <admin@frogg.fr>
 *
 *
 * @Route(
 *     {
 *     "fr": "/mon-compte",
 *     "en": "/my-account"
 *     },
 *     name="account_"
 * )
 */
class AccountController extends Controller
{

    /**
     * Route to User option management
     *
     * @Route(
     *     {
     *     "fr": "/option",
     *     "en": "/option"
     *     },
     *     name="option",
     *     methods={"GET","POST"}
     * )
     * @return Response
     */
    public function option()
    {
        return new Response("TODO");
    }
}
