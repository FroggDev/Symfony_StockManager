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

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductManager
{

    /** @var Request */
    private $request;

    /**
     * ProductManager constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getProductFromBarcode()
    {
        $barcode=$this->request->query->get('barcode');

        // ==> get bar code in database
        // ==> else scrap from web

        exit($barcode);

    }
}