<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Stock;

use App\Entity\Product;
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
     * ProductManager constructor.
     * @param Request $request
     * @param ObjectManager $manager
     * @param ProductScraper $scraper
     */
    public function __construct(RequestStack $request,EntityManagerInterface $manager,ProductScraper $scraper)
    {
        $this->request = $request->getMasterRequest();
        $this->manager = $manager;
        $this->scraper = $scraper;
    }

    public function getProductFromBarcode()
    {
        // get barcode from request
        $barcode=$this->request->query->get('b');

        // Get product from barcode
        $product = $this
            ->manager
            ->getRepository(Product::class)
            ->findByBarcode($barcode);

        if(!$product){
            $product = $this->scraper->scrap($barcode);
        }else{
            echo "PRODUCT IS IN DATABASE !";
        }

        dump($product);
        exit($barcode);

    }

}