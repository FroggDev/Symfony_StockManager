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
use App\Tests\Util\AbstractUserFixture;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Tests\Encoder\PasswordEncoder;

/**
 * @author Frogg <admin@frogg.fr>
 */
class ScrapTest extends WebTestCase
{
    /** @const int nb of fixture to create */
    private const NBUSERFIXTURE = 1;

    /** @var Client */
    private $client;

    /** @var Router */
    private $router;

    /** @var EntityManager */
    private $entityManager;

    /** @var PasswordEncoder */
    private $passwordEncoder;

    /*###################
     # BEFORE EACH TESTS#
     ###################*/

    public function setUp()
    {
        // create a client
        $this->client = static::createClient();

        // get entity manager from container
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');


        $this->passwordEncoder = self::$kernel->getContainer()->get('security.password_encoder');

        // get router from container
        $this->router = self::$kernel->getContainer()->get('router');

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

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testScrap()
    {
        // INIT
        //-----

        $barcode= "3057640234163";

        $testuser = new User();
        $testuser
            ->setEnabled()
            ->setFirstName('froggTEst')
            ->setLastName('froggTEst')
            ->setEmail('test@frogg.fr')
            ->setPassword($this->passwordEncoder->encodePassword($testuser,'test'));

        // insert into database
        $this->entityManager->persist($testuser);
        $this->entityManager->flush();


        // NAVIGATION
        //-----------

        $this->client->followRedirects(true);

        // create a new crawler
        $crawler = $this->client->request(
            'GET',
            $this->router->generate('security_connexion', ['_locale' => 'en'])
        );

        // select the form and fill in some values
        $form = $crawler->filter('FORM[name=connexion]')->form();
        $form['_username'] = $testuser->getEmail();
        $form['_password'] = 'test';

        // set lang for test
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-EN';

        // submits the given form
        $this->client->submit($form);

        //Create a new product from barcode
        $this->client->request('GET', $this->router->generate('stock_ajax_barcode',['barcode' => $barcode]) );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame($data['result'],'ok');
        $this->assertSame($data['barcode'],$barcode);

        //Get an existing product from barcode
        $this->client->request('GET', $this->router->generate('stock_ajax_barcode',['barcode' => $barcode]) );
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertSame($data['result'],'ok');
        $this->assertSame($data['barcode'],$barcode);
    }
}