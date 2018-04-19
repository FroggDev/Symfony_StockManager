<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Common\Traits\Product;


/**
 * @author Frogg <admin@frogg.fr>
 */
trait FolderTrait
{
    /**
     * Generate the image product folder from barcode
     * @param String $barcode
     *
     * @return string
     */
    private function getFolder(String $barcode): string
    {
        preg_match('/([\d]{3})([\d]{3})([\d]{3})(.*)/', $barcode, $matches);
        array_shift($matches);

        return implode('/', $matches) . '/';
    }
}
