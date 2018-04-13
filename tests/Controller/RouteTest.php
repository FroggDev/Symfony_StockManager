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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class RouteTest extends WebTestCase
{


    public function setUp()
    {
        $client = static::createClient();
    }

    public function testAllRoutes()
    {


/*
 *  TODO CHECK IF /(mon|my)-stock url then redirect login ?
 *
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

                    echo $path;


                    # enable symfony profiler (before the request !)
                    $client->enableProfiler();
                    $client->request('GET', $path);
                    # check if response is 200 OK
                    $this->isValidResponse($client,"Error on page ".$path ."with code " . $client->getResponse()->getStatusCode());
                    # @ TODO test if reponse < 500ms (return 0)
                    # check if display page in less than 500 ms
                    echo "1==>=============";
                    $time = $client->getProfile()->getCollector('time')->getDuration();
                    echo $time;
                    echo "1==>===========";
                    #echo $time;
                    $this->assertLessThan(500,$time, "Page is too long to load = ".$time);

        }*/
    }

    /*################
     # UTILS METHODS #
     ################*/

    /**
     * @param $client
     */
    /*
    public function isValidResponse(Client $client)
    {
        $this->assertEquals(
            200 ,
            $client->getResponse()->getStatusCode(),
            "Page returned status code : " . $client->getResponse()->getStatusCode()
        );
    }*/
}