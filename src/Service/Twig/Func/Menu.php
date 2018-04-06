<?php
namespace App\Service\Twig\Func;

use App\Service\Twig\AbstractTwigExtension;
use Symfony\Component\HttpFoundation\RequestStack;

class Menu extends AbstractTwigExtension
{
    /**@ var Request  */
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

        if ($route == $stack->get('_route')){
            echo "active ";
        }
    }
}