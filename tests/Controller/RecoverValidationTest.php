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

use App\Entity\User;
use App\SiteConfig;
use App\Tests\Util\AbstractUserFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class RecoverValidationTest extends WebTestCase
{

    /** @const int nb of fixture to create */
    private const NBUSERFIXTURE = 3;

    /** @const int the user id to test */
    private const USERID = 1;

    /** @var Client */
    private $client;

    /** @var Router */
    private $router;

    /** @var EntityManager */
    private $entityManager;

    /** @var string */
    private $page;

    /*###################
     # BEFORE EACH TESTS#
     ###################*/

    public function setUp()
    {
        // create a client
        $this->client = static::createClient();

        //Check if test envirnoement
        AbstractUserFixture::checkEnvironement(self::$kernel);

        // get router from container
        $this->router = self::$kernel->getContainer()->get('router');

        // get translator from container
        $this->translator = self::$kernel->getContainer()->get('translator');

        // get entity manager from container
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        // get register page uri
        $this->page = $this->router->generate('security_recover_validation', ['_locale' => 'en']);
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
        $this->assertNull(AbstractUserFixture::createFixtures(self::$kernel, self::NBUSERFIXTURE));
    }


    /*############
     # MAIN TESTS#
     ############*/

    public function testRecoverValidation()
    {
        // NAVIGATION
        //-----------

        $user = $this->setUserStatus('Disabled');

        $originalPass = $user->getPassword();

        // create a new crawler
        $crawler = $this->client->request(
            'GET',
            $this->page,
            ['email'=>$user->getEmail(),'token'=>$user->getToken()]
        );

        $text = $crawler->filter('H1')->eq(0)->text();


        // select the form and fill in some values
        $form = $crawler->filter('FORM[name=user_password]')->form();
        $form['user_password[password]'] = 'NewPassword';


        // submits the given form
        $crawler = $this->client->submit($form);
        $this->client->followRedirect();

        // USER NOT FOUND ?????
        //dump($this->client->getResponse());

        //get user edited from database
        $dbUser = $this->getUser();

        // TEST
        //-----

        //test page title
        //$this->assertSame($text,  $this->translator->trans('changepassword title', [], 'security_form'));

        //dump($originalPass);
        //dump($dbUser->getPassword());

        //test user password
        //$this->assertNotSame($originalPass,  $dbUser->getPassword());

        //test if user is enable
        $this->assertTrue($dbUser->isEnabled());
    }

    /*################
     # UTILS METHODS #
     ################*/

    /**
     * @param string $status
     *
     * @return User
     */
    private function setUserStatus(string $status): User
    {
        //set status action
        $action = "set$status";

        /** @var User $fakeUser */
        $fakeUser = $this->getUser();
        $fakeUser->$action();

        // insert into database
        $this->entityManager->persist($fakeUser);
        $this->entityManager->flush();

        return $fakeUser;
    }

    /**
     * @return User
     */
    private function getUser() : User
    {
        return $this->entityManager->getRepository(User::class)->find(self::USERID);
    }

}