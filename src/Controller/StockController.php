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
use App\Repository\ProductRepository;
use App\Repository\StockProductsRepository;
use App\SiteConfig;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
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
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function home(EntityManagerInterface $manager)
    {
        /** @var StockProductsRepository $repository */
        $repository = $manager->getRepository(StockProducts::class);

        $expired = $repository->findList($this->getUser()->getStock()->getId(), 0);

        $expire3 = $repository->findList($this->getUser()->getStock()->getId(), 3);

        $expire7 = $repository->findList($this->getUser()->getStock()->getId(), 7);

        // display page from twig template
        return $this->render(
            'stock/home.html.twig',
            [
                'expired' => $expired[2],
                'expire3' => $expire3[2],
                'expire7' => $expire7[2],
            ]
        );
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
        return $this->render('stock/form_scan.html.twig',['type' => 'add']);
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
        // Display form view
        return $this->render('stock/form_scan.html.twig',['type' => 'del']);
    }

    /**
     * Route to list product from stock
     *
     * @Route(
     *     {
     *     "fr": "/liste.html/{currentPage?1}/{order?1}/{inDay?}",
     *     "en": "/list.html/{currentPage?1}/{order?1}/{inDay?}"
     *     },
     *     name="list",
     *     methods={"GET","POST"}
     * )
     * @param EntityManagerInterface $manager
     * @param string                 $currentPage
     * @param string                 $order
     * @param null|string            $inDay
     *
     * @return Response
     */
    public function list(EntityManagerInterface $manager, string $currentPage, string $order, ?string $inDay)
    {
        /** @var StockProductsRepository $repository */
        $repository = $manager->getRepository(StockProducts::class);

        $stockProducts = $repository
            ->findList($this->getUser()->getStock()->getId(), $inDay, (int) $currentPage, $order);

        // Display product list
        return $this->render(
            'stock/list.html.twig',
            [
                'stockProducts' => $stockProducts[1],
                'currentPage' => $currentPage,
                'countPagination' => ceil($stockProducts[0] / SiteConfig::NBPERPAGE),
                'order' => $order,
                'inDay' => $inDay,
                'from' => 'stock_list',
                'barcode' => null,
                'search' => null
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
     *     "fr": "/produit.html/{barcode<[^/]*>?}",
     *     "en": "/product.html/{barcode<[^/]*>?}"
     *     },
     *     name="add_product",
     *     methods={"GET","POST"}
     * )
     *
     * @Entity("product",expr="repository.findOneByBarcode(barcode)")
     *
     * @param Product $product
     *
     * @return Response
     */
    public function showProduct(Product $product)
    {
        // Display form view
        return $this->render('stock/form_add_to_stock.html.twig', ['product' => $product]);
    }

    /*######
     # DEL #
     ######*/

    /**
     * Route to display a product from barcode
     *
     * @Route(
     *     {
     *     "fr": "/supprimer/produit.html/{barcode<[^/]*>?}/{currentPage<[^/]*>?1}/{order<[^/]*>?1}",
     *     "en": "/delete/product.html/{barcode<[^/]*>?}/{currentPage<[^/]*>?1}/{order<[^/]*>?1}"
     *     },
     *     name="del_product",
     *     methods={"GET","POST"}
     * )
     *
     * @return Response
     */
    public function showDelProduct(Request $request,EntityManagerInterface $manager,string $currentPage, string $order,?string $barcode)
    {
        $search = $request->get('search');

        $productId = null;

        if(null!==$barcode && ""!==$barcode) {
            $product = $manager->getRepository(Product::class)->findOneByBarcode($barcode);
            $productId = $product->getId();
        }

        /** @var StockProductsRepository $repository */
        $repository = $manager->getRepository(StockProducts::class);

        $stockProducts = $repository
            ->findList(
                $this->getUser()->getStock()->getId(),
                9,
                (int) $currentPage,
                $order,
                $productId,
                $search
            );

        // Display product list
        return $this->render(
            'stock/list.html.twig',
            [
                'stockProducts' => $stockProducts[1],
                'currentPage' => $currentPage,
                'countPagination' => ceil($stockProducts[0] / SiteConfig::NBPERPAGE),
                'search' => $search,
                'order' => $order,
                'inDay' => null,
                'barcode' => $barcode,
                'from' => 'stock_del_product'
            ]
        );
    }

    /*#########
     # DETAIL #
     #########*/

    /**
     * Route to product detail
     *
     * @Route(
     *     {
     *     "fr": "/fiche-produit.html/{barcode<[^/]*>?}",
     *     "en": "/product-description.html/{barcode<[^/]*>?}"
     *     },
     *     name="product_card",
     *     methods={"GET","POST"}
     * )
     *
     * @Entity("product",expr="repository.findOneByBarcode(barcode)")
     *
     * @param Product $product
     *
     * @return Response
     */
    public function showProductCard(Product $product)
    {
        return $this->render('stock/product.html.twig', ['product' => $product]);
    }

    /*#########
     # SEARCH #
     #########*/

    /**
     * Route to display search result
     *
     * @Route(
     *     {
     *     "fr": "/produit/recherche.html/{currentPage?1}",
     *     "en": "/product/search.html/{currentPage?1}"
     *     },
     *     name="product_result",
     *     methods={"GET","POST"}
     * )
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param string                 $currentPage
     *
     * @return Response
     */
    public function showProductResult(Request $request, EntityManagerInterface $manager, string $currentPage)
    {
        //string search
        $search = $request->get('search');

        /** @var ProductRepository $repository */
        $repository = $manager->getRepository(Product::class);

        //request in database
        $result = $repository->findAddSearch($search, $currentPage);

        return $this->render(
            'stock/search.html.twig',
            [
                'products' => $result[1],
                'search' => $search,
                'currentPage' => $currentPage,
                'countPagination' => ceil($result[0] / SiteConfig::NBPERPAGE),
            ]
        );
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
     *
     * @return Response
     */
    public function createProduct()
    {
        return new Response('TODO');
    }
}
