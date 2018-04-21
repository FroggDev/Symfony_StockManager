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

use App\Common\Traits\Html\ATagGeneratorTrait;
use App\Service\Twig\AbstractTwigExtension;
use Symfony\Component\Asset\Packages;
use Symfony\Component\Routing\RouterInterface;

/**
 * @author Frogg <admin@frogg.fr>
 */
class Link extends AbstractTwigExtension
{
    use ATagGeneratorTrait;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Packages
     */
    public $asset;

    /**
     * constructor.
     * @param RouterInterface $router
     * @param Packages $asset
     */
    public function __construct(RouterInterface $router, Packages $asset)
    {
        $this->router = $router;
        $this->asset = $asset;
    }

    /**
     * @param string|null $class
     * @param string|null $text
     * @param array|null $parameters
     * @return string
     */
    public function getLink(
        string $route,
        string $text = null,
        array $parameters = null,
        string $class = null
    ): string
    {
        return $this->getATag(
            $this->router->generate($route, $parameters),
            $text,
            $class
        );
    }
}