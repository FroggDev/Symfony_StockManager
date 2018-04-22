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

use App\Service\Stock\ProductManager;
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
class StockAjaxController extends Controller
{
    /*###########
     # AJAX GET #
     ###########*/

    /**
     * Route to ajax get product from barcode
     *
     * @Route(
     *     {
     *     "fr": "/ajax/produit.{_format<json|html>?json}",
     *     "en": "/ajax/product.{_format<json|html>?json}"
     *     },
     *     name="ajax_barcode",
     *     methods={"GET"}
     * )
     *
     * @param ProductManager $productManager
     *
     * @return Response
     *
     * @throws \App\Exception\Product\ProductTypeException
     */
    public function ajaxCodeBar(ProductManager $productManager)//(ProductCommand $productManager)
    {
        //Could be JsonResponse::fromJsonString();
        // but this is not requiered as format 'json' is set to json in the route
        return new Response($productManager->getProductFromBarcode());
    }

    /*###########
     # AJAX ADD #
     ###########*/

    /**
     * Route to ajax remove product from stock
     *
     * @Route(
     *     {
     *     "fr": "/ajax/add.{_format<json|html>?json}",
     *     "en": "/ajax/ajout.{_format<json|html>?json}"
     *     },
     *     name="ajax_add",
     *     methods={"GET","POST"}
     * )
     * @param ProductManager $manager
     * @param Request        $request
     *
     * @return Response
     */
    public function ajaxAdd(ProductManager $manager, Request $request)
    {
        $manager->doAjaxAdd($this->getUser());

        // no equivalent in JsonResponse ?
        return new Response(json_encode($request->request->all()));
    }

    /**
     * Route to ajax cancel action add fom stock
     *
     * @Route(
     *     {
     *     "fr": "/ajax/annulation/ajout.{_format<json>?json}",
     *     "en": "/ajax/cancel/add.{_format<json>?json}"
     *     },
     *     name="ajax_cancel_add",
     *     methods={"GET"}
     * )
     * @param ProductManager $manager
     * @param Request        $request
     *
     * @return Response
     */
    public function ajaxCancelAdd(ProductManager $manager, Request $request)
    {
        $manager->doAjaxCancelAdd($this->getUser());

        return new Response(json_encode($request->query->all()));
    }

    /*#############
    # AJAX REMOVE #
    ##############*/

    /**
     * Route to ajax remove product from stock
     *
     * @Route(
     *     {
     *     "fr": "/ajax/remove.{_format<json|html>?json}",
     *     "en": "/ajax/supprimer.{_format<json|html>?json}"
     *     },
     *     name="ajax_remove",
     *     methods={"GET"}
     * )
     * @param ProductManager $manager
     *
     * @return Response
     */
    public function ajaxRemove(ProductManager $manager)
    {
        return new Response(json_encode($manager->doAjaxRemove($this->getUser())));
    }

    /**
     * Route to ajax cancel action remove fom stock
     *
     * @Route(
     *     {
     *     "fr": "/ajax/annulation/suppression.{_format<json|html>?json}",
     *     "en": "/ajax/cancel/remove.{_format<json|html>?json}"
     *     },
     *     name="ajax_cancel_remove",
     *     methods={"GET","POST"}
     * )
     * @param ProductManager $manager
     *
     * @return Response
     */
    public function ajaxCancelRemove(ProductManager $manager)
    {
        $result = $manager->doAjaxCancelRemove($this->getUser());

        return new Response($result);
    }
}
