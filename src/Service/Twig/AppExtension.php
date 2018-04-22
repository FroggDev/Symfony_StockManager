<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Service\Twig;

use App\Service\Twig\Func\Link;
use App\Service\Twig\Func\Menu;
use App\Service\Twig\Func\Product;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Custom twig filter
 * @url https://symfony.com/doc/current/templating/twig_extension.html
 */
class AppExtension extends \Twig_Extension
{

    /**
     * Twig calls :
     * {{ string  | maxLen(47) }}
     * @return array
     */
    /*
    public function getFilters(): array
    {
        return [
            new \Twig_Filter('maxLen', [StringAppRuntime::class, 'maxLength']),
        ];
    }
    */

    /**
     * @return array
     *
     * Twig calls :
     * {{ getActiveMenu("route_name") }}
     * {{ asset( getProductImage(product.barcode,product.picture) ) }}
     * {{ getExpires( product,app.user.stock )  }}
     * {{ getNutritionalInfo(product) | raw }}
     * {{ getFormatedList( product.packagings ) }}
     * {{ getLink('route',test,{params:params},"class") }}
     */
    public function getFunctions(): array
    {
        return [
            // Menu selection
            new \Twig_Function('getActiveMenu', [Menu::class, 'getActiveMenu']),
            // Image product
            new \Twig_Function('getProductImage', [Product::class, 'getProductImage']),
            // Expiration dates
            new \Twig_Function('getDateExpires', [Product::class, 'getDateExpires']),
            // Product nutrition info
            new \Twig_Function('getNutritionalInfo', [Product::class, 'getNutritionalInfo']),
            // Product relation list
            new \Twig_Function('getFormatedList', [Product::class, 'getFormatedList']),
            // Global link generator
            new \Twig_Function('getLink', [Link::class, 'getLink']),
        ];
    }
}
