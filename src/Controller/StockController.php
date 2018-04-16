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
 *     "fr": "/mon-stock",
 *     "en": "/my-stock"
 *     },
 *     name="stock_"
 * )
 */
class StockController extends Controller
{

    /**
     * Route to Stock home (registered default home)
     *
     * @Route(
     *     {
     *     "fr": "/accueil.html",
     *     "en": "/home.html"
     *     },
     *     name="home",
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function home()
    {
        // display page from twig template
        return $this->render('stock/home.html.twig');
    }

    /**
     * Route to add to Stock
     *
     * @Route(
     *     {
     *     "fr": "/ajouter.html",
     *     "en": "/add.html"
     *     },
     *     name="add",
     *     methods={"GET","POST"}
     * )
     * @return Response
     */
    public function add()
    {
        // Display form view
        return $this->render('stock/form_add.html.twig');
    }

    /**
     * Route to remove from Stock
     *
     * @Route(
     *     {
     *     "fr": "/supprimer.html",
     *     "en": "/remove.html"
     *     },
     *     name="del",
     *     methods={"GET","POST"}
     * )
     * @return Response
     */

    public function del()
    {
        return new Response("TODO");
    }

    /**
     * Route to list product from stock
     *
     * @Route(
     *     {
     *     "fr": "/liste.html",
     *     "en": "/list.html"
     *     },
     *     name="list",
     *     methods={"GET","POST"}
     * )
     * @return Response
     */
    public function list()
    {
        return new Response("TODO");
    }
}
