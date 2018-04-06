<?php
namespace App\Service\Twig;

use App\Service\Twig\Func\Menu;

/**
 * Class AppExtension
 * @package App\Service\Twig
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
     */
    public function getFunctions(): array
    {
        return [
            # Menu selection
            new \Twig_Function('getActiveMenu', [Menu::class, 'getActiveMenu'])
        ];
    }


}