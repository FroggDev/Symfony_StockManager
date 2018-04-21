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
     * @param Request $request
     * @param ObjectManager $manager
     * @param ProductScraper $scraper
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

        // Get product from barcode
        $product = $this
            ->manager
            ->getRepository(Product::class)
            ->findOneByBarcode($barcode);

        // get the product from scrap
        if (!$product) {
            $product = $this->scraper->scrap($barcode);
        }

        // no result found
        if (!$product) {
            return '{  "result" : "ok" , "barcode" : "' . $barcode . '"  }';
        }

        return '{  "result" : "ok" , "name" : "' . $product->getName() . '" , "barcode" : "' . $barcode . '" , "generic" : "' . $product->getCommonName() . '"}';
    }
}