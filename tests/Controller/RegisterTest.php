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
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class RegisterTest extends WebTestCase
{
    /** @var Client */
    private $client;

    /** @var Router */
    private $router;

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

        // get register page uri
        $this->page = $this->router->generate('security_register');
    }

    /*############
     # MAIN TESTS#
     ############*/

    public function testRegisterUser()
    {
        // INIT
        //-----

        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('fake@fake.fr');

        // NAVIGATION
        //-----------

        // do navigation
        $crawler = $this->formNavigatation($fakeUser);

        // get mail info from profiler
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // TEST
        //-----

        //TODO HERE TEST IF PAGE IS 200 AND GOOD TARGET
        //$crawler

        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());

        // Check email informations
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame(SiteConfig::SITENAME . ' - ' . $this->translator->trans('email account validation subject', [], 'security_mail'), $message->getSubject());
        $this->assertSame(SiteConfig::SECURITYMAIL, key($message->getFrom()));
        $this->assertSame($fakeUser->getEmail(), key($message->getTo()));
    }

    /*################
     # NEGATIVE TESTS#
     ################*/

    public function testRegisterUserWithoutFirstName()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('', [], '')
        );
    }

    public function testRegisterUserWithoutLastName()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('')
            ->setPassword('Fake')
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('', [], '')
        );
    }

    public function testRegisterUserWithoutPassword()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('')
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('', [], '')
        );
    }


    public function testRegisterUserWithoutInvalidEmail()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('fake@fake');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
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
        $form = $crawler->filter('FORM[name=user]')->form();
        $form['user[firstname]'] = $fakeUser->getFirstName();
        $form['user[lastname]'] = $fakeUser->getLastName();
        $form['user[email]'] = $fakeUser->getEmail();
        $form['user[password]'] = $fakeUser->getPassword();

        // submits the given form
        return $this->client->submit($form);

    }
}