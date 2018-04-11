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

use App\Entity\User;
use App\Tests\Fixture\AbstractUserFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @author Frogg <admin@frogg.fr>
 *
 * Using CommandTester & KernelTestCase
 * @see https://symfony.com/doc/current/console.html
 */
class UserManagerTest extends KernelTestCase
{

    /** @var Application */
    static private $application;

    /** @var Command */
    static private $command;

    /** @var EntityManager */
    static private $emanager;

    /*#######################
     # ONCE BEFORE ALL TEST #
     #######################*/

    static public function setUpBeforeClass()
    {
        // Get the kernel
        self::$kernel = self::bootKernel();

        // Make sure we are in the test environment
        if ('test' !== self::$kernel->getEnvironment()) {
            throw new \LogicException('Primer must be executed in the test environment');
        }

        self::$application = new Application(self::$kernel);
        self::$application->setAutoExit(false);

        // Get the commmand
        self::$command = self::$application->find('app:userManager');

        // Get entity manager
        self::$emanager = self::$kernel->getContainer()->get('doctrine')->getManager();
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
     * Test the Display User
     */

    /*
    public function testDisplayUserList(): void
    {

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // Set input scenario
        $commandTester->setInputs([0]); // display user list
        $commandTester->setInputs([1]); // display user list 4.1
        $commandTester->setInputs([5]); // exit

        // Execute the command
        $commandTester->execute(['command' => self::$command->getName()]);

        ////$output = $commandTester->getDisplay();
        ////$this->assertContains('Username: Wouter', $output);

    }

    **
     * Test the Enable User
     *
    public function testEnableUser(): void
    {
        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // Set input scenario
        $commandTester->setInputs([2]); // enable account
        $commandTester->setInputs([1]); // user id
        $commandTester->setInputs([5]); // exit

        // Execute the command
        $this->assertNull($commandTester->execute(['command' => self::$command->getName()]));


        //TODO TEST IF USER IS ENABLED
    }

    **
     * Test the Add role to User
     *
    public function testAddRoleToUser(): void
    {
        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // Set input scenario
        $commandTester->setInputs([3]); // add role
        $commandTester->setInputs([2]); // user id
        $commandTester->setInputs([0]); // add role EDITOR
        $commandTester->setInputs([5]); // exit

        // Execute the command
        $this->assertNull($commandTester->execute(['command' => self::$command->getName()]));

        //TODO TEST IF USER 2 HAS ROLE EDITOR
        //TODO TEST IF USER 2 ENABLED
        //TODO TEST IF USER 3 HAS NO ROLE EDITOR
    }

    **
     * Test the Remove role to User
     *
    public function testDelRoleToUser(): void
    {
        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // Set input scenario
        $commandTester->setInputs([4]); // remove role
        $commandTester->setInputs([2]); // user id
        $commandTester->setInputs([1]); // remove role EDITOR
        $commandTester->setInputs([5]); // exit

        // Execute the command
        $this->assertNull($commandTester->execute(['command' => self::$command->getName()]));

        //TODO TEST IF USER 2 NO ROLE EDITOR
    }
    */

    /*##################
     # SPECIFICS TESTS #
     ##################*/
    /*
     * TODO SPECIFICS TEST
     * */

}
