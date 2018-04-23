<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Command;

use App\Command\UserCommand;
use App\Entity\Product;
use App\Entity\User;
use App\Tests\Util\AbstractUserFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Using CommandTester & KernelTestCase
 * @see https://symfony.com/doc/current/console.html
 */
class CommandsTest extends KernelTestCase
{

    private $barcode = ['3250390779100','3256224398264','8002270015786','7613035530799','4062300126664','3178530412925','3240930514025'];

    /** @var Application */
    static private $application;

    /** @var ObjectManager */
    static private $repository;

    /*#######################
     # ONCE BEFORE ALL TEST #
     #######################*/

    static public function setUpBeforeClass()
    {
        // Get the kernel
        self::$kernel = self::bootKernel();

        self::$application = new Application(self::$kernel);
        self::$application->setAutoExit(false);

        // Get entity repository
        self::$repository = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(Product::class);

    }

    /*###########
     # DB TESTS #
     ###########*/

    /**
     * Test the Create Database & user
     */
    public function testToCreateDatabaseWithFixturedUsers(): void
    {
        // nothing should occur
        $this->assertNull(AbstractUserFixture::createDatabase(self::$kernel));

        // nothing should occur
        $this->assertNull(AbstractUserFixture::createFixtures(self::$kernel,3));
    }

    /*#############
     # MAIN TESTS #
     #############*/

    /**
     * Test the Create Database & user
     * @throws \Exception
     */
    /*
    public function testCommandDatabase(): void
    {
        $command = self::$application->find('app:database');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => 'app:database',
            'action' => 'create',
        ));

        $output = $commandTester->getDisplay();
        //$this->assertContains('Username: Wouter', $output);

        dump($output);
        //exit();
    }
    */

    /**
     * Test the Main Menu
     * @throws \Exception
     */
    public function testProductCommand(): void
    {
        // INIT
        //-----

        $command = self::$application->find('app:product:add');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => 'app:product:add',
            'barcode' => $this->barcode
        ));

        $output = $commandTester->getDisplay();

        $result = self::$repository->findAll();

        // TEST
        //-----

        $this->assertContains('[OK]', $output);

        $this->assertSame(count($result),count($this->barcode));

        foreach($result as $product){
            $this->assertTrue(in_array($product->getBarcode(),$this->barcode));
        }
    }
}
