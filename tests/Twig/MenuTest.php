<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Twig;

use App\Service\Twig\AbstractTwigExtension;
use App\Service\Twig\Func\Menu;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Frogg <admin@frogg.fr>
 */
class TestMenu extends KernelTestCase
{

    private const ROUTE1 = 'test.html';
    private const ROUTE2 = 'other.html';

    /** @var RequestStack */
    private $requestStack;

    /*#######################
     # ONCE BEFORE EACH TEST #
     #######################*/

    public function setUp()
    {
        // INIT
        //-----

        self::$kernel = self::bootKernel();

        //create a request
        $request = new FakeRequest();
        $request->set('_route', self::ROUTE1);

        //requeststack
        $this->requestStack = new RequestStack();
        $this->requestStack->push($request);
    }

    /**
     * Test if menu is selected
     */
    function testMenuInstance()
    {
        // INIT
        //-----

        $menu = new Menu($this->requestStack);

        // TEST
        //-----

        $this->assertInstanceOf(AbstractTwigExtension::class, $menu);
    }


    /**
     * Test if menu is selected
     */
    function testMenuSelection()
    {
        // INIT
        //-----

        $menu = new Menu($this->requestStack);

        // TEST
        //-----

        $this->expectOutputString('active ');

        $menu->getActiveMenu(self::ROUTE1);

    }

    /**
     * Test if menu is not selected
     */
    function testMenuNoSelection()
    {
        // INIT
        //-----

        //menu
        $menu = new Menu($this->requestStack);

        // TEST
        //-----

        $this->assertNull($menu->getActiveMenu(self::ROUTE2));

        $this->assertInstanceOf('App\Service\Twig\AbstractTwigExtension',$menu);
    }
}

/**
 * Class FakeRequest a MANUAL MADE MOCK
 * @package App\Tests\Twig
 */
class FakeRequest extends Request
{
    private $data = [];

    /**
     * @param string $key
     * @param string $value
     */
    function set(string $key, string $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    function get($key, $default = NULL)
    {
        return $this->data[$key];
    }
}