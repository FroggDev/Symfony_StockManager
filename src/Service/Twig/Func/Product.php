<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Service\Twig\Func;

use App\Common\Traits\Product\FolderTrait;
use App\Service\Twig\AbstractTwigExtension;
use App\SiteConfig;

/**
 * @author Frogg <admin@frogg.fr>
 */
class Product extends AbstractTwigExtension
{

    use FolderTrait;

    /**
     * @param string $barcode
     * @param string $image|null
     *
     * @return string
     */
    public function getProductImage(string $barcode, ?string $image) : string
    {
        return SiteConfig::UPLOADPATH.$this->getFolder($barcode).$image;
    }

}