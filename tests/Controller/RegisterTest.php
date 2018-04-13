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
    /** @const int nb of fixture to create */
    private const NBUSERFIXTURE = 3;

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
        $this->page = $this->router->generate('security_register',['_locale' => 'en']);
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
        $this->assertNull(AbstractUserFixture::createFixtures(self::$kernel,self::NBUSERFIXTURE));
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
        $originalPassword = 'Fake';
        $originalEmail = 'addinbase@fake.fr';
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword($originalPassword)
            ->setEmail($originalEmail);

        // NAVIGATION
        //-----------

        // do navigation
        $crawler = $this->formNavigatation($fakeUser);

        // get mail info from profiler
        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        // TEST
        //-----

        // checks that an email was sent
        $this->assertSame(1, $mailCollector->getMessageCount());

        // Check email informations
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame(SiteConfig::SITENAME . ' - ' . $this->translator->trans('email account validation subject', [], 'security_mail'), $message->getSubject());
        $this->assertSame(SiteConfig::SECURITYMAIL, key($message->getFrom()));
        $this->assertSame($fakeUser->getEmail(), key($message->getTo()));

        // TEST USER IN DATABASE
        //----------------------

        /** @var user $testuser get user created ni database */
        $testuser =
            self::$kernel
                ->getContainer()
                ->get('doctrine.orm.entity_manager')
                ->getRepository(User::class)
                ->find(self::NBUSERFIXTURE+1);

        // test if user exist in database
        $this->assertNotNull($testuser, "User should not be null");

        // test if email is correct
        $this->assertSame($testuser->getEmail(),$originalEmail);

        // test if password has been encoded
        $this->assertNotEquals($testuser->getPassword(),$originalPassword);
    }

    /*################
     # NEGATIVE TESTS#
     ################*/

    // ********* MAIN

    public function testRegisterUserIfAlreadyExit()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('addinbase@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('email already in use', [], 'validators')
        );
    }

    public function testRegisterUserWithInvalidEmail()
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
            $this->translator->trans('email is not valid', [], 'validators')
        );
    }

    // ********* EMPTY FIELD

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
            $this->translator->trans('firstname should not be blank', [], 'validators')
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
            $this->translator->trans('lastname should not be blank', [], 'validators')
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
            $this->translator->trans('password should not be blank', [], 'validators')
        );
    }

    public function testRegisterUserWithoutEmail()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('email should not be blank', [], 'validators')
        );
    }

    // ********* TOO LONG

    public function testRegisterUserWithTooLongFirstName()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstNameFakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('firstname is too long', [], 'validators')
        );
    }

    public function testRegisterUserWithtTooLongLastName()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastNameFakeLastName')
            ->setPassword('Fake')
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('lastname is too long', [], 'validators')
        );
    }

    public function testRegisterUserWithtTooLongPassword()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('FakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFakeFake')
            ->setEmail('fake@fake.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('password is too long', [], 'validators')
        );
    }

    public function testRegisterUserWithtTooLongEmail()
    {
        // init a fake user
        $fakeUser = new User();
        $fakeUser
            ->setFirstName('FakeFirstName')
            ->setLastName('FakeLastName')
            ->setPassword('Fake')
            ->setEmail('reallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemailreallylongemail@yahou.fr');

        // Check if error occured
        $this->errorNavigation(
            $fakeUser,
            $this->translator->trans('email is too long', [], 'validators')
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

        /**
         * TODO CLEAN THIS
         */

        // get html error block
        if($crawler->filter('.securityerror UL LI')->count())
        {
        $text = $crawler->filter('.securityerror UL LI')->eq(0)->text();
        }
        else{
            $text =  $crawler->filter('.flash-notice H6 DIV')->eq(0)->text();
        }

        // TEST
        //-----

        // test if an error html is present
        $this->assertNotNull($text,"No error occured, but should trigger " . $message);

        //test error message
        $this->assertSame(
            $text,
            $message
        );

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