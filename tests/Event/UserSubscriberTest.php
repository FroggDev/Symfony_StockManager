<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Event;

use App\Entity\User;
use App\EventSubscriber\UserSubscriber;
use App\Exception\Account\AccountAccessDeniedException;
use App\SiteConfig;
use App\Tests\Fixture\AbstractUserFixture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserSubscriberTest extends WebTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var User */
    private $enabledUser;
    /** @var User */
    private $disabledUser;

    /**
     * Test if can add to database
     */
    public function testDatabase()
    {
        self::$kernel = self::bootKernel();

        // nothing should occur
        $this->assertNull(AbstractUserFixture::createDatabase(self::$kernel));

        // nothing should occur
        $this->assertNull(AbstractUserFixture::createFixtures(self::$kernel,3));

        //create the entity manager
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /*############
     # MAIN TEST #
     ############*/

    /**
     * Test if user last connexion has been set in database when login
     */
    public function testIfUserConnexionHasBeenSet()
    {
        // INIT
        //-----

        self::$kernel = self::bootKernel();

        //create the entity manager
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->enabledUser = $this->entityManager->getRepository(User::class)->find(1);
        //else cannot login
        $this->enabledUser->setEnabled();

        $interarctiveLoginEvent = $this->createInteractiverEvent($this->enabledUser);

        // CREATE
        //-----

        $userSubscriber = new UserSubscriber(
            $this->entityManager,
            self::$kernel->getContainer()->get('event_dispatcher')
        );

        // TEST
        //-----

        //test if last connexion is null by default
        $this->assertNull($this->enabledUser->getLastConnexion());

        //Test if all was ok
        $this->assertNull($userSubscriber->onSecurityInteractiveLogin($interarctiveLoginEvent));

        //test if last connexion has been set
        $this->assertNotNull($this->enabledUser->getLastConnexion());

        //test if last connexion has been set in database
        $this->enabledUser = $this->entityManager->getRepository(User::class)->find(1);
        $this->assertNotNull($this->enabledUser->getLastConnexion());

        //test if last connexion of user user hasnt been created too
        $this->disabledUser = $this->entityManager->getRepository(User::class)->find(2);
        $this->assertNull($this->disabledUser->getLastConnexion());

        $events = $userSubscriber->getSubscribedEvents();
        $this->assertCount(1,$events);
    }

    /**
     * Test if user has cookie set when login
     */
    public function testIfUserCookieHasBeenSet()
    {
        // INIT
        //-----

        self::$kernel = self::bootKernel();

        $response = new Response();

        $filterResponseEvent = new FilterResponseEvent(
            self::$kernel,
            new Request(),
            200,
            $response
        );

        //create the entity manager
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->enabledUser = $this->entityManager->getRepository(User::class)->find(1);
        //else cannot login
        $this->enabledUser->setEnabled();

        // CREATE
        //-------

        $interarctiveLoginEvent = $this->createInteractiverEvent($this->enabledUser);

        $userSubscriber = new UserSubscriber(
            $this->entityManager,
            self::$kernel->getContainer()->get('event_dispatcher')
        );

        $userSubscriber->onSecurityInteractiveLogin($interarctiveLoginEvent);

        $userSubscriber->onKernelResponse($filterResponseEvent);

        // TEST
        //-----

        $this->assertEquals($response->headers->getCookies()[0]->getName(),SiteConfig::COOKIEUSERNAME);
        $this->assertEquals($response->headers->getCookies()[0]->getValue(),$this->enabledUser->getEmail());
    }


    /**
     * Test if not enabled user can login
     */
    public function testIfUserNotEnabledCanLogin()
    {

        // INIT
        //-----

        self::$kernel = self::bootKernel();

        //create the entity manager
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        $this->disabledUser = $this->entityManager->getRepository(User::class)->find(2);
        //else cannot login
        $this->disabledUser->setDisabled();

        // CREATE
        //-------

        $userSubscriber = new UserSubscriber(
            $this->entityManager,
            self::$kernel->getContainer()->get('event_dispatcher')
        );


        // TEST
        //-----

        $this->expectException(AccountAccessDeniedException::class);

        $userSubscriber->onSecurityInteractiveLogin(
            $this->createInteractiverEvent($this->disabledUser)
        );

    }

    /*################
     # UTILS METHODS #
     ################*/

    /**
     * @param User $user
     *
     * @return InteractiveLoginEvent
     */
    function createInteractiverEvent(User $user) :InteractiveLoginEvent
    {

        $tokenInterface = new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            '777',
            $user->getRoles()
        );

        // MOCK
        //-----

        $interarctiveLoginEvent = $this
            ->getMockBuilder(InteractiveLoginEvent::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getAuthenticationToken'))
            ->getMock();
        $interarctiveLoginEvent
            ->method('getAuthenticationToken')
            ->willReturn($tokenInterface);

        return $interarctiveLoginEvent;
    }
}
