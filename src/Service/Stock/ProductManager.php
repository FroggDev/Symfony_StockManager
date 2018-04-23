<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Stock;

use App\Entity\Product;
use App\Entity\StockProducts;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\StockProductsRepository;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductManager
{

    /** @var Request */
    private $request;
    /**
     * @var ObjectManager
     */
    private $manager;
    /**
     * @var ProductScraper
     */
    private $scraper;

    /**
     * ProductCommand constructor.
     * @param RequestStack           $request
     * @param EntityManagerInterface $manager
     * @param ProductScraper         $scraper
     */
    public function __construct(RequestStack $request, EntityManagerInterface $manager, ProductScraper $scraper)
    {
        $this->request = $request->getMasterRequest();
        $this->manager = $manager;
        $this->scraper = $scraper;
    }

    /**
     * @param string|null $barcode
     *
     * @return string
     *
     * @throws \App\Exception\Product\ProductTypeException
     */
    public function getProductFromBarcode(string $barcode = null)
    {
        // get barcode from request
        if (null === $barcode) {
            $barcode = $this->request->query->get('barcode');
        }

        /** @var ProductRepository $repository */
        $repository = $this->manager->getRepository(Product::class);

        // Get product from barcode
        $product = $repository->findOneByBarcode($barcode);

        // get the product from scrap
        if (!$product) {
            $product = $this->scraper->scrap($barcode);
        }

        // no result found
        if (!$product) {
            return '{  "result" : "ok" , "barcode" : "'.$barcode.'"  }';
        }

        return '{  "result" : "ok" , "name" : "'.$product->getName().'" , "barcode" : "'.$barcode.'" , "generic" : "'.$product->getCommonName().'"}';
    }

    /**
     * Add a product using ajax
     * @param User $user
     */
    public function doAjaxAdd(User $user)
    {
        // init generated ids
        $ids = [];

        /**
         * Get datas from form
         */
        $expire = $this->request->get('expire');
        $nbProduct = $this->request->get('nbproductfield');
        $idproduct = $this->request->get('idproduct');
        $dateExpire = null;

        /**
         * TODO ADAPT THIS WITH LOCALE !!
         */
        if ("" !== $expire) {
            $dateExpire = DateTime::createFromFormat('M j, Y', $expire);
        }

        /** @var ProductRepository $repository */
        $repository = $this->manager->getRepository(Product::class);

        // get result from database
        $product = $repository->findOneById($idproduct);

        for ($w = 0; $w < (int) $nbProduct; $w++) {
            $stockProduct = new StockProducts();

            $stockProduct
                ->setDateExpire($dateExpire)
                ->setProduct($product)
                ->setStock($user->getStock());

            $this->manager->persist($stockProduct);
            $this->manager->flush();

            $ids[] = $stockProduct->getId();
        }

        //add to request
        $this->request->request->add(['productIds' => implode(',', $ids)]);
        $this->request->request->add(['result' => 'ok']);
    }

    /**
     * Cancel ajax add
     * @param User $user
     */
    public function doAjaxCancelAdd(User $user) : void
    {
        //Get datas from form
        $productIds = explode(",", $this->request->get('productIds'));

        /** @var StockProductsRepository $repository */
        $repository = $this->manager->getRepository(StockProducts::class);

        foreach ($productIds as $id) {
            //Security : check user stock id
            $stockProduct = $repository->findOneBy(['id' => $id, 'stock' => $user->getStock()]);

            $this->manager->remove($stockProduct);
            $this->manager->flush();
        }
    }

    /**
     * @return array
     */
    public function doAjaxRemove(User $user) : array
    {
        // Get datas from request
        $ids = explode(',', $this->request->get('ids'));

        /** @var array $return */
        $return = [];

        /** @var StockProductsRepository $repository */
        $repository = $this->manager->getRepository(StockProducts::class);

        foreach ($ids as $id) {
            //Security : check user stock id
            /** @var StockProducts $stockProduct */
            $stockProduct = $repository->findOneBy(['id' => $id, 'stock' => $user->getStock()]);

            $return['data'][] = $this->getFormatedData($stockProduct);

            $this->manager->remove($stockProduct);
            $this->manager->flush();
        }

        $return['name'] = $this->request->get('name');
        $return['result'] = 'ok';

        return $return;
    }

    /**
     * @return string
     */
    public function doAjaxCancelRemove(User $user) : string
    {
        //init ids returned
        $ids = [];

        //init data returned
        $data = [];

        //data from json
        $datas = $this->request->get('data');

        /** @var ProductRepository $repository */
        $repository = $this->manager->getRepository(Product::class);

        $postedData = json_decode($datas);
        foreach ($postedData->data as $data) {
            $stockProduct = new StockProducts();


            $stockProduct
                ->setDateExpire($data->dateExpire!==null?$this->stringToDatetime($data->dateExpire->date):null)
                ->setDateCreation($data->dateCreation!==null?$this->stringToDatetime($data->dateCreation->date):null)
                ->setProduct($repository->findOneBy(['id' => $data->productId]))
                ->setStock($user->getStock());

            $this->manager->persist($stockProduct);
            $this->manager->flush();

            $ids[] = $stockProduct->getId();
        }

        return '{  "result" : "ok" , "ids" : "'.implode(',', $ids).'" , "name" : "'.$postedData->name.'" }';
    }

    /*##########
     # PRIVATE #
     ##########*/

    /**
     * @param StockProducts $stockProduct
     *
     * @return array
     */
    private function getFormatedData(StockProducts $stockProduct) : array
    {
        $data['dateCreation'] = $stockProduct->getDateCreation();
        $data['dateExpire'] = $stockProduct->getDateExpire();
        $data['productId'] = $stockProduct->getProduct()->getId();

        return $data;
    }

    /**
     * @param string $date
     *
     * @return DateTime
     */
    private function stringToDatetime(string $date)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s.u', $date);
    }
}
