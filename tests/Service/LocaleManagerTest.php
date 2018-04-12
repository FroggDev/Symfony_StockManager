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
use App\Security\UserChecker;
use App\Security\UserManager;
use App\Service\LocaleManager;
use App\Service\MailerManager;
use App\SiteConfig;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @author Frogg <admin@frogg.fr>
 */
class LocaleManagerTest extends KernelTestCase
{

    /**
     * WITH REAL SERVICES :
     * @see php bin/console debug:container
     *
     * self::$kernel = self::bootKernel();
     * self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
     * self::$kernel->getContainer()->get('request_stack');
     * self::$kernel->getContainer()->get('mailer');
     * self::$kernel->getContainer()->get('translator');
     * self::$kernel->getContainer()->get('twig');
     */

    /** @var LocaleManager */
    private $localeManager;


    const TOLOCALE = 'fr';
    const TOURI = '/accueil.html';

    const FROMLOCALE = 'en';
    const FROMURI = '/home.html';

    /*#######################
     # ONCE BEFORE EACH TEST #
     #######################*/

    public function setUp()
    {
        // INIT
        //-----


        self::$kernel = self::bootKernel();

        // FAKING THE REQUEST GET LANG FROM URL
        // ------------------------------------

        // HEADER BAG
        $headerBag = $this
            ->getMockBuilder(HeaderBag::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $headerBag
            ->method('get')
            ->with('referer')
            ->willReturn(self::FROMURI);

        // PARAMETER BAG
        $parameterbag = $this
            ->getMockBuilder( ParameterBag::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();
        $parameterbag
            ->method('get')
            ->with(SiteConfig::COOKIELOCALENAME)
            ->willReturn(self::TOLOCALE);


        // REQUEST
        $request = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getLocale', 'setLocale', 'get'))
            ->getMock();
        $request
            ->method('getLocale')
            ->will($this->returnValue(self::FROMLOCALE));
        $request
            ->method('get')
            ->with(SiteConfig::COOKIELOCALENAME)
            ->will($this->returnValue(self::TOLOCALE));
        $request->headers = $headerBag;
        $request->cookies = $parameterbag;

        // REQUESTSTACK
        $requestStack = $this->getMockBuilder(RequestStack::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getMasterRequest'))
            ->getMock();
        $requestStack
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        // INIT CLASS
        // ----------

        $this->localeManager = new LocaleManager(
            self::$kernel->getContainer()->get('router'),
            $requestStack
        );
    }

    /*######################
     # Localemanager TESTS #
     ######################*/

    /**
     * Try to switch locale en to fr from /home.html to /accueil.html
     */
    public function testChangeLocaleWhenASkedByUser()
    {
        // INIT CLASS
        // ----------

        $responseResult = $this->localeManager->changeSelectedLocale();

        // TEST
        //-----

        //check if is response
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $responseResult);

        //check if redirect is good
        $this->assertEquals(self::TOURI, $responseResult->headers->get('location'));

        // check if the cookie is set
        $this->assertEquals($responseResult->headers->getCookies()[0]->getName(),SiteConfig::COOKIELOCALENAME);
        $this->assertEquals($responseResult->headers->getCookies()[0]->getValue(),self::TOLOCALE);
    }

    /**
     * Try to switch locale based on user cookie en to fr from /home.html to /accueil.html
     */
    public function testChangeDefaultLocaleWhenASkedByUser()
    {
        // INIT CLASS
        // ----------

        $responseResult = $this->localeManager->changeDefaultLocale();

        // TEST
        //-----

        // check if is good response
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $responseResult);

        // check if redirect is good
        $this->assertEquals(self::TOURI, $responseResult->headers->get('location'));
    }
}
