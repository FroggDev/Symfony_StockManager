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

use App\Entity\Product;
use App\Entity\StockProducts;
use App\Service\Stock\ProductManager;
use App\SiteConfig;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Tests\RequestTest;
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

    /*#######
     # HOME #
     #######*/

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
     *     "fr": "/liste.html/{currentPage?1}/{order?1}",
     *     "en": "/list.html/{currentPage?1}/{order?1}"
     *     },
     *     name="list",
     *     methods={"GET","POST"}
     * )
     * @param EntityManagerInterface $manager
     * @param string $currentPage
     * @param string $order
     *
     * @return Response
     */
    public function list(EntityManagerInterface $manager, string $currentPage, string $order)
    {
        // get the selected request order
        switch ($order) {
            case 2:
                $requestOrder = 'sp.dateCreation';
                break;
            case 3:
                $requestOrder = 'p.name';
                break;
            default:
                $requestOrder = 'sp.dateExpire';
        }

        // Get all datas linked to stock id (with limit)
        $stockProducts = $manager
            ->getRepository(StockProducts::class)
            ->findByGroupedProduct(
                $this->getUser()->getStock()->getId(),
                (int)$currentPage,
                $requestOrder
            );

        // Display product list
        return $this->render(
            'stock/list.html.twig',
            [
                'stockProducts' => $stockProducts,
                'currentPage' => $currentPage,
                'countPagination' => 5,
                'order' => $order
            ]
        );
    }

    /*######
     # ADD #
     ######*/

    /**
     * Route to display a product from barcode
     *
     * @Route(
     *     {
     *     "fr": "/produit.html/{barcode<[^/]*>?}/{from<.*>?}",
     *     "en": "/product.html/{barcode<[^/]*>?}/{from<.*>?}"
     *     },
     *     name="product",
     *     methods={"GET","POST"}
     * )
     *
     * @Entity("product",expr="repository.findOneByBarcode(barcode)")
     * @param Product $product
     * @param null|string $from
     *
     * @return Response
     */
    public function showProduct(Product $product, ?string $from)
    {
        // Display form view
        return $this->render('stock/form_add_to_stock.html.twig', ['from' => $from, 'product' => $product]);
    }

    /*#########
     # DETAIL #
     #########*/


    /**
     * Route to product detail
     *
     * @Route(
     *     {
     *     "fr": "/fiche-produit.html",
     *     "en": "/product-description.html"
     *     },
     *     name="product_card",
     *     methods={"GET","POST"}
     * )
     * @return Response
     */
    public function showProductCard()
    {
        return new Response("TODO");
    }

    /*#######
     # LIST #
     #######*/

    /**
     * Route to display search result
     *
     * @Route(
     *     {
     *     "fr": "/resultat.html",
     *     "en": "/result.html"
     *     },
     *     name="result",
     *     methods={"GET","POST"}
     * )
     * @return Response
     */
    public function showResult()
    {
        return new Response("TODO");
    }

    /*#######
     # AJAX #
     #######*/

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
     * @param ProductManager $productManager
     *
     * @return Response
     */
    public function ajaxCodeBar(ProductManager $productManager)//(ProductCommand $productManager)
    {
        return new Response($productManager->getProductFromBarcode());
    }

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
     * @param Request $request
     *
     * @return Response
     */
    public function ajaxAdd(Request $request, EntityManagerInterface $manager)
    {
        /**
         * TODO : PUT THIS SOMEWHERE MORE CLEAN
         */

        /**
         * Get datas from form
         */
        $expire = $request->get('expire');
        $nbProduct = $request->get('nbproductfield');
        //$barcode = $request->get('barcode');
        $idproduct = $request->get('idproduct');
        $dateExpire = null;

        /**
         * TODO ADAPT THIS WITH LOCALE !!
         */

        if ("" !== $expire) {
            $dateExpire = DateTime::createFromFormat('M j, Y', $expire);
        }

        $product = $manager->getRepository(Product::class)->findOneById($idproduct);

        $ids = [];

        for ($w = 0; $w < (int)$nbProduct; $w++) {
            $stockProduct = new StockProducts();

            $stockProduct
                ->setDateExpire($dateExpire)
                ->setProduct($product)
                ->setStock($this->getUser()->getStock());

            $manager->persist($stockProduct);
            $manager->flush();

            $ids[] = $stockProduct->getId();
        }

        //add to request
        $request->request->add(['productIds' => implode(",", $ids)]);
        $request->request->add(['result' => 'ok']);

        return new Response(json_encode($request->request->all()));
    }

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
     * @return Response
     */
    public function ajaxRemove(Request $request, EntityManagerInterface $manager)
    {
        /**
         * Get datas from request
         */
        $ids = explode(',', $request->get('ids'));


        /** @var array $return */
        $return = [];

        foreach ($ids as $id) {

            $StockProduct = $manager
                ->getRepository(StockProducts::class)
                ->findOneById($id);

            $return['data'][] = $this->getFormatedData($StockProduct);

            $manager->remove($StockProduct);
            //$manager->flush();
        }

        $return['name'] = $request->get('name');
        $return['result'] = 'ok';

        return new Response(json_encode($return));
    }


    /**
     * @param StockProducts $StockProduct
     */
    private function getFormatedData(StockProducts $StockProduct)
    {
        $data['dateCreation'] = $StockProduct->getDateCreation();
        $data['dateExpire'] = $StockProduct->getDateExpire();
        $data['productId'] = $StockProduct->getProduct()->getId();

        return $data;
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
     * @return Response
     */
    public function ajaxCancelRemove(Request $request, EntityManagerInterface $manager)
    {

        $ids = [];

        $datas = $request->get('data');


        var_dump($datas);
        exit();



        foreach($datas as $data){

            dump($data);

            $stockProduct = new StockProducts();

            $stockProduct
                ->setDateExpire( $data['dateExpire'])
                ->setDateCreation($data['dateCreation'])
                ->setProduct($manager->getRepository(Product::class)->findOne($data['productId']))
                ->setStock($this->getUser()->getStock());

            //$manager->persist($stockProduct);
            //$manager->flush();

            $ids[]=$stockProduct->getId();

        }
        exit();
        return new Response(json_encode(['result' => 'ok','ids' => $ids,'name' => $data['name']]));
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
     * @param RequestTest $request
     * @param EntityManager $manager
     * @return Response
     */
    public function ajaxCancelAdd(Request $request, EntityManagerInterface $manager)
    {
        /**
         * TODO : PUT THIS SOMEWHERE MORE CLEAN
         */

        /**
         * Get datas from form
         */

        $productIds = explode(",", $request->get('productIds'));

        foreach ($productIds as $id) {
            $stockProduct = $manager
                ->getRepository(StockProducts::class)
                ->findOneById($id);

            $manager->remove($stockProduct);
            $manager->flush();
        }

        //$request->request->add(['result' => 'ok']);

        return new Response(json_encode($request->query->all()));
    }

    /*########
     # OTHER #
     ########*/

    /**
     * Route to create a product from scratch
     *
     * @Route(
     *     {
     *     "fr": "/produit/creation.html",
     *     "en": "/product/create.html"
     *     },
     *     name="product_create",
     *     methods={"GET"}
     * )
     * @return Response
     */
    public function createProduct()
    {
        return new Response("TODO");
    }


}
