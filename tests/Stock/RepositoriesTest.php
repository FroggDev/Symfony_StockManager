<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Tests\Security;

use App\Entity\Country;
use App\Entity\Product;
use App\Entity\StockProducts;
use App\Tests\Util\AbstractUserFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class RepositoriesTest extends KernelTestCase
{

    /**
     * TODO ADD PRODUCT AND STOCK PRODUCT FIXTURES
     */

    /** @var EntityManager */
    static private $emanager;

    /*#######################
     # ONCE BEFORE ALL TEST #
     #######################*/

    /**
     * @throws \Exception
     */
    static public function setUpBeforeClass()
    {
        //Get the Kernel
        self::$kernel = self::bootKernel();

        AbstractUserFixture::createDatabase(self::$kernel);

        // Get entity manager
        self::$emanager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /*###################
     # Repository TESTS #
     ##################*/

    /**
     * Test find all in country table
     */
    function testFindAllCountry()
    {
        // get all countries
        $countryList = self::$emanager->getRepository(Country::class)->findAll();

        $this->assertCount(241,$countryList);
    }

    /**
     * Test search products table
     */
    function testSearchProduct()
    {
        // get all products
        $productList = self::$emanager->getRepository(Product::class)->findAddSearch('a', 1);

        $this->assertCount(2,$productList);
        $this->assertEquals($productList[0],0);
        $this->assertCount(0,$productList[1]);
    }

    /**
     * Test search stockproducts table
     */
    function testStockProduct()
    {
        $repository = self::$emanager->getRepository(StockProducts::class);

        $expired = $repository->findDateExpires(1, 1);

        $listed = $repository->findList(1, 1, 1, '3', null, 'a');

        $this->assertCount(0,$expired);
        $this->assertCount(3,$listed);
    }
}