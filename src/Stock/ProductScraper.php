<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Stock;

//use Symfony\Bundle\FrameworkBundle\Client;
use Goutte\Client;
/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductScraper
{
    /**
     * @var Client
     */
    private $client;

    /**
     * ProductScraper constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $barcode
     */
    public function scrap(string $barcode)
    {

        $this->client->followRedirects(true);

        // create a new crawler
        $this->client->request(
            'POST',
            'https://fr.openfoodfacts.org/cgi/session.pl',
            ['user_id' => 'stock@frogg.fr', 'password' => 'scrapOFF']
        );

        $crawler = $this->client->request(
            'GET',
        'https://fr.openfoodfacts.org/cgi/product.pl?type=edit&code='.$barcode
        );

        dump($barcode);
        //dump($crawler);
        echo($crawler->html());


    }

}