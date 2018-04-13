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

use App\Entity\User;
use App\Tests\Util\AbstractUserFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserRepositoryTest extends KernelTestCase
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

    /*#######################
     # UserRepository TESTS #
     #######################*/

    /**
     * Test find all in user table
     */
    function testFindAll()
    {
        // Number of fixtures to add
        $amount = 5;

        // Create the fixtures
        AbstractUserFixture::createFixtures(self::$kernel,$amount);

        // get all users
        $userList = self::$emanager->getRepository(User::class)->findAll(true);

        // check number in table
        $this->assertCount(
            $amount,
            $userList,
            "Number of user in table user should be equals to " . $amount
        );

        /** @var User $user */
        foreach($userList as $user){
            $this->assertNotNull($user->getFirstName());
            $this->assertNotNull($user->getLastName());
            $this->assertNotNull($user->getEmail());
            $this->assertNotNull($user->getPassword());
        }

    }
}