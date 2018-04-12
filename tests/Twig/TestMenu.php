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

use App\Service\Twig\Func\Menu;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Frogg <admin@frogg.fr>
 */
class TestMenu extends KernelTestCase
{

    /**
     * Test if menu is selected
     */
    function testMenuSelection()
    {

        $route = 'test.html';

        // INIT
        //-----

        $request = new FakeRequest();

        $request->set('_route', $route);

        $requestStack = new RequestStack();

        $requestStack->push($request);

        $menu = new Menu($requestStack);

        // TEST
        //-----

        $this->expectOutputString('active ');

        $menu->getActiveMenu($route);

    }

    /**
     * Test if menu is not selected
     */
    function testMenuNoSelection()
    {

        $route = 'test.html';
        $anotherRoute = 'other.html';

        // INIT
        //-----

        $request = new FakeRequest();

        $request->set('_route', $route);

        $requestStack = new RequestStack();

        $requestStack->push($request);

        $menu = new Menu($requestStack);

        // TEST
        //-----

        $this->assertNull($menu->getActiveMenu($anotherRoute));

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
     * @return mixed
     */
    function get($key, $default = NULL)
    {
        return $this->data[$key];
    }
}