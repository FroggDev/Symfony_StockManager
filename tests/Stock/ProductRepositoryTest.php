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
use App\Entity\User;
use App\Tests\Util\AbstractUserFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class ProductRepositoryTest extends KernelTestCase
{

    /** @var EntityManager */
    static private $emanager;

    /*#######################
     # ONCE BEFORE ALL TEST #
     #######################*/

    static public function setUpBeforeClass()
    {
        //Get the Kernel
        self::$kernel = self::bootKernel();

        AbstractUserFixture::createDatabase(self::$kernel);

        // Get entity manager
        self::$emanager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /*##########################
     # CountryRepository TESTS #
     ##########################*/

    /**
     * Test find all in country
     */
    function testFindAllCountries()
    {
        // get all country
        $countryList = self::$emanager->getRepository(Country::class)->findAll();

        // check type
        $this->assertSame('array',gettype($countryList));

        /** @var Country $country */
        foreach($countryList as $country){
            $this->assertNotNull($country->getName());
            $this->assertNotNull($country->getAlpha2());
            $this->assertNotNull($country->getAlpha3());
            $this->assertNotNull($country->getCode());
        }
    }

    /*##########################
     # ProductRepository TESTS #
     ##########################*/

    /**
     * Test find all in products
     */
    function testFindAllProducts()
    {
        // get all country
        $productList = self::$emanager->getRepository(Product::class)->findAll();

        // check type
        $this->assertSame('array',gettype($productList));
    }

}