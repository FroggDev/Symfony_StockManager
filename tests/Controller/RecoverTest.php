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
class RecoverTest extends WebTestCase
{
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
        $this->page = $this->router->generate('security_recover');
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
        $this->assertNull(AbstractUserFixture::createFixtures(self::$kernel, 1));
    }

    /*############
     # MAIN TESTS#
     ############*/

    public function testRecoverUserDisabledUser()
    {
        // INIT
        //-----
        $fakeUser = $this->setUserStatus('Disabled');

        // NAVIGATION
        //-----------

        // do navigation
        $crawler = $this->formNavigatation($fakeUser);

        // TEST
        //-----

        $this->doResult($fakeUser);
    }

    public function testRecoverUserEnabledUser()
    {
        // INIT
        //-----
        $fakeUser = $this->setUserStatus('Enabled');

        // NAVIGATION
        //-----------

        // do navigation
        $crawler = $this->formNavigatation($fakeUser);

        // TEST
        //-----

        $this->doResult($fakeUser);
    }

    /*################
     # NEGATIVE TESTS#
     ################*/

    public function testRecoverWithWrongEmail()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('', [], '')
        );
    }

    public function testRecoverWithAccountBanned()
    {
        // Check if error occured
        $this->errorNavigation(
            $this->setUserStatus('Banned'),
            $this->translator->trans('', [], '')
        );
    }

    public function testRecoverWithAccountClosed()
    {
        // Check if error occured
        $this->errorNavigation(
            $this->setUserStatus('Closed'),
            $this->translator->trans('', [], '')
        );
    }

    public function testRecoverWithAccountDeleted()
    {
        // Check if error occured
        $this->errorNavigation(
            $this->setUserStatus('Deleted'),
            $this->translator->trans('', [], '')
        );
    }

    /*################
     # UTILS METHODS #
     ################*/

    /**
     * @param User $fakeUser
     * @param string $message
     */
    private function errorNavigation(User $fakeUser, string $message)
    {
        // NAVIGATION
        //-----------

        // do navigation
        $crawler = $this->formNavigatation($fakeUser);

        // get mail info from profiler
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // TEST
        //-----

        //TODO HERE TEST IF TRIGGER ERROR MESSAGE
        //$crawler / $message

        // checks that an email was sent
        $this->assertSame(0, $mailCollector->getMessageCount());
    }

    /**
     * @param User $fakeUser
     *
     * @return mixed
     */
    private function formNavigatation(User $fakeUser)
    {
        // create a new crawler
        $crawler = $this->client->request('GET', $this->page);

        // enables the profiler for the next request (it does nothing if the profiler is not available)
        $this->client->enableProfiler();

        // select the form and fill in some values
        $form = $crawler->filter('FORM[name=user_recover]')->form();
        $form['user_recover[email]'] = $fakeUser->getEmail();

        // submits the given form
        return $this->client->submit($form);
    }

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
        $fakeUser = $this->entityManager->getRepository(User::class)->find(1);
        $fakeUser->$action();

        // insert into database
        $this->entityManager->persist($fakeUser);
        $this->entityManager->flush();

        return $fakeUser;
    }


    /**
     * @param User $fakeUser
     * @param $mailCollector
     */
    private function doResult(User $fakeUser) : void
    {
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        //TODO HERE TEST IF PAGE IS 200 AND GOOD TARGET
        //$crawler

        //test if user has token recover set
        $this->assertNotNull($fakeUser->getToken());

        if($fakeUser->isEnabled()){
            //test if user has token validity (has enabled first)
            $this->assertNotNull($fakeUser->getTokenValidity());
        }else{
            //test if user has no token validity (has not enabled first)
            $this->assertNull($fakeUser->getTokenValidity());
        }

        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());

        // Check email informations
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame(SiteConfig::SITENAME . ' - ' . $this->translator->trans('email password recovery subject', [], 'security_mail'), $message->getSubject());
        $this->assertSame(SiteConfig::SECURITYMAIL, key($message->getFrom()));
        $this->assertSame($fakeUser->getEmail(), key($message->getTo()));
    }
}