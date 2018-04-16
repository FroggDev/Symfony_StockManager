<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class RouteTest extends WebTestCase
{
    /** @var Router */
    private $router;

    public function setUp()
    {
        self::$kernel = static::bootKernel();

        // get router from container
        $this->router = self::$kernel->getContainer()->get('router');
    }

    public function testAllRoutes()
    {

        $maxTime = 100000;

        $client = static::createClient();

        // enable symfony profiler (before the request !)
        $client->enableProfiler();

        $client->request('GET', '/');

        $router = $client->getContainer()->get('router');
        $routesCollection = $router->getRouteCollection();

        foreach ($routesCollection as $route) {
            $tokens = $route->compile()->getTokens();


            foreach ($tokens as $token) {
                if ('text' === $token[0]) {
                    $path = $token[1];
                }
            }

            if ($path === '/' || $path === '/_error') {
                continue;
            }

            // enable symfony profiler (before the request !)
            $client->enableProfiler();
            $client->request('GET', $path);
            // check if response is 200 OK
            $this->isValidResponse($client, "Error on page '" . $path . "' with code " . $client->getResponse()->getStatusCode());
            // check if display page in less than maxTime ms

            $time = $client->getProfile()->getCollector('time')->getDuration();
            //echo $time;
            $this->assertLessThan($maxTime, $time, "Page " . $path . " is too long to load = " . $time . "ms");
        }
    }

    /**
     * CHANGE LANG
     */

    public function testChangeLang()
    {
        $client = static::createClient();

        // create a new crawler
        $client->request('GET', '/');

        // create a new crawler
        $crawler = $client->request(
            'GET',
            $this->router->generate('change_locale', ['_locale' => 'en']),
            ['locale' => 'en']
        );

        $this->assertEquals(
            301,
            $client->getResponse()->getStatusCode()
        );

    }

    public function testChangeLangError()
    {
        $client = static::createClient();

        try {
            // create a new crawler
            $client->request(
                'GET',
                $this->router->generate('change_locale', ['_locale' => 'en']),
                ['locale' => 'en']
            );
        } catch (\Error $error) {
            $this->assertContains("setRedirectUri() must be of the type string", $error->getMessage());
        }
    }

    /*################
     # UTILS METHODS #
     ################*/

    /**
     * @param $client
     */
    private function isValidResponse(Client $client, string $msg)
    {
        $code = $client->getResponse()->getStatusCode();
        if ($code !== 301 && $code !== 302) {
            $this->assertEquals(
                200,
                $client->getResponse()->getStatusCode(),
                $msg
            );
        }
    }
}