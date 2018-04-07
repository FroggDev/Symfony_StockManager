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

use App\Service\Twig\AbstractTwigExtension;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Frogg <admin@frogg.fr>
 */
class Menu extends AbstractTwigExtension
{
    /**@ var Request $requestStack the user request */
    private $requestStack;

    /**
     * Menu constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Twig Function, write "active" in menu class if is current route
     * @param string $route
     */
    public function getActiveMenu(string $route) : void
    {
        $stack = $this->requestStack->getMasterRequest();

        if ($route === $stack->get('_route')) {
            echo "active ";
        }
    }
}
