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

use App\Command\UserManager;
use App\Entity\User;
use App\Tests\Fixture\AbstractUserFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

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

    /** @var ObjectManager */
    static private $repository;

    /*#######################
     # ONCE BEFORE ALL TEST #
     #######################*/

    static public function setUpBeforeClass()
    {
        // Get the kernel
        self::$kernel = self::bootKernel();

        // Make sure we are in the test environment
        if ('test' !== self::$kernel->getEnvironment()) {
            throw new \LogicException('Test must be executed in the test environment');
        }

        self::$application = new Application(self::$kernel);
        self::$application->setAutoExit(true);

        // Get the commmand
        self::$command = self::$application
            ->find('app:userManager');

        // Get entity repository
        self::$repository = self::$kernel
            ->getContainer()
            ->get('doctrine')
            ->getManager()
            ->getRepository(User::class);

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
     * Test the Main Menu
     */
    public function testDisplayMainMenu(): void
    {
        // INIT
        //-----

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // SCENARIO
        //---------

        // Set input scenario
        $commandTester->setInputs([5]); // exit

        // TEST
        //-----

        // Execute the command
        $this->assertEquals(
            UserManager::EXITCODE,
            $commandTester->execute(['command' => self::$command->getName()])
        );

        $output = $commandTester->getDisplay();

        //Test if content is well displayed
        $this->assertContains('Welcome to User Role Manager', $output);
    }

    /**
     * Test the Display User
     */
    public function testDisplayUserList(): void
    {
        // INIT
        //-----

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // SCENARIO
        //---------

        // Set input scenario
        $commandTester->setInputs([0]); // display user list
        $commandTester->setInputs([0]); // continue to display user list ? (0=no)
         $commandTester->setInputs([5]); // exit

        // TEST
        //-----

        // Execute the command
        $this->assertEquals(
            UserManager::EXITCODE,
            $commandTester->execute(['command' => self::$command->getName()])
            );

         $output = $commandTester->getDisplay();

        //Test if content is well displayed
        $this->assertContains('Displaying user list using SymfonyStyle !', $output);

        //var_dump($output);
    }

    /**
     * Test the Display User version 4.1
     */
    public function testDisplayUserListNew(): void
    {
        // INIT
        //-----

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // SCENARIO
        //---------

        // Set input scenario
        $commandTester->setInputs([1]); // display user list 4.1
        $commandTester->setInputs([5]); // exit

        // TEST
        //-----

        // Execute the command
        $this->assertEquals(
            UserManager::EXITCODE,
            $commandTester->execute(['command' => self::$command->getName()])
        );

        $output = $commandTester->getDisplay();

        //Test if content is well displayed
        $this->assertContains('Displaying user list using 4.1 feature !', $output);


        //var_dump($output);

    }



    /**
     * Test the Enable User
     */
    public function testEnableUser(): void
    {
        // INIT
        //-----

        $userId = 1;

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // SCENARIO
        //---------

        // Set input scenario
        $commandTester->setInputs([2]); // enable account
        $commandTester->setInputs([$userId]); // user id
        $commandTester->setInputs([5]); // exit

        // TEST
        //-----

        // Execute the command
        $this->assertEquals(
            UserManager::EXITCODE,
            $commandTester->execute(['command' => self::$command->getName()])
        );

        $output = $commandTester->getDisplay();

        $user = self::$repository->find($userId);

        // check if user has been enabled in database
        $this->assertTrue($user->isEnabled());

        //var_dump($output);
    }

    /**
     * Test the Add role to User
     */
    public function testAddRoleToUser(): void
    {
        // INIT
        //-----

        $userId = 2;

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // SCENARIO
        //---------

        // Set input scenario
        $commandTester->setInputs([3]); // add role
        $commandTester->setInputs([$userId]); // user id
        $commandTester->setInputs([0]); // add role EDITOR
        $commandTester->setInputs([5]); // exit

        // Execute the command
        $this->assertEquals(
            UserManager::EXITCODE,
            $commandTester->execute(['command' => self::$command->getName()])
        );

        $user = self::$repository->find($userId);

        // check if user has role editor added
        $this->assertTrue($user->hasRole('ROLE_EDITOR'));
        // check if user is enabled
        $this->assertTrue($user->isEnabled());

        $user = self::$repository->find(3);

        // check if user has not role editor added
        $this->assertNotTrue($user->hasRole('ROLE_EDITOR'));
        // check if user is not enabled
        $this->assertNotTrue($user->isEnabled());

        //var_dump($output);
    }

    /**
     * Test the Remove role to User
     */
    public function testDelRoleToUser(): void
    {
        // INIT
        //-----

        $userId = 2;

        // Get the command tester
        $commandTester = new CommandTester(self::$command);

        // SCENARIO
        //---------

        // Set input scenario
        $commandTester->setInputs([4]); // remove role
        $commandTester->setInputs([$userId]); // user id
        $commandTester->setInputs([1]); // remove role EDITOR
        $commandTester->setInputs([5]); // exit

        // Execute the command
        $this->assertEquals(
            UserManager::EXITCODE,
            $commandTester->execute(['command' => self::$command->getName()])
        );

        $user = self::$repository->find($userId);

        // check if user has no more the role editor
        $this->assertNotTrue($user->hasRole('ROLE_EDITOR'));
        // check if user is still enabled
        $this->assertTrue($user->isEnabled());

        //var_dump($output);
    }

    /*##################
     # SPECIFICS TESTS #
     ##################*/
    /*
     * TODO SPECIFICS TEST
     * */

}
