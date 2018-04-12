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
use App\Service\MailerManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserManagerTest extends KernelTestCase
{

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserManager */
    private $usermanager;

    /** @var MailerManager */
    private $mailerManager;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var User */
    private $user;

    /*#######################
     # ONCE BEFORE EACH TEST #
     #######################*/

    public function setUp()
    {
        /**
         * WITH REAL SERVICES :
         * @see php bin/console debug:container
         *
            self::$kernel = self::bootKernel();
            self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
            self::$kernel->getContainer()->get('request_stack');
            self::$kernel->getContainer()->get('mailer');
            self::$kernel->getContainer()->get('translator');
            self::$kernel->getContainer()->get('twig');
         */

        /**
         * FAKING USER
         * -----------
         * Only for database test
         */
        $this->user = new User();

        $this->user
            ->setPassword('Fake Password')
            ->setEmail('Fake Email');

        /**
         * FAKING entityManager
         * --------------------
         *
         * @see https://stackoverflow.com/questions/24416028/is-there-a-way-to-create-a-mock-of-doctrine-2s-entity-manager-and-supply-it-a-m
         *
         * Infering that the the Subject Under Test is dealing with a single
         * repository.
         */
        $repository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('findOneByEmail'))
            ->getMock();

        $repository
            ->method('findOneByEmail')
            ->will($this->returnValue($this->user));

        /**
         * Now mock the EntityManager that will return the aforementioned
         * Repository. Extend to more repositories with a returnValueMap or
         * with clauses in case you need to handle more than one repository.
         */
        $this->entityManager = $this
            ->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->entityManager
            ->method('getRepository')
            ->with('App\Entity\User')
            ->will($this->returnValue($repository));

        /**
         * FAKING entityManager
         * --------------------
         *
         * hand made !
         *
         * mutiple call
         * @see https://stackoverflow.com/questions/5988616/phpunit-mock-method-multiple-calls-with-different-arguments/5989095
         */

        // PARAMETER BAG

        $parameterbag = $this
            ->getMockBuilder( ParameterBag::class)
            ->disableOriginalConstructor()
            ->setMethods(array('get'))
            ->getMock();

        $parameterbag
            ->method('get')
            ->with(
                $this->logicalOr(
                    $this->equalTo('email'),
                    $this->equalTo('token')
                )
            )
            ->will($this->returnCallback(array($this, 'getRequestCallBack')));


        // REQUEST

        $request = $this->createMock(Request::class);

        $request->query = $parameterbag;

        // REQUESTSTACK

        $requestStack = $this->getMockBuilder(RequestStack::class)
             ->disableOriginalConstructor()
             ->setMethods(array('getMasterRequest'))
             ->getMock();

        $requestStack
            ->method('getMasterRequest')
            ->will($this->returnValue($request));

        /*
        var_dump($requestStack->getMasterRequest()->query->get('email'));
        var_dump($requestStack->getMasterRequest()->query->get('token'));
        */

        /**
         * FAKING other injections
         * -----------------------
         */
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->mailerManager = $this->createMock(MailerManager::class);

        $translator = $this->createMock(TranslatorInterface::class);

        $twig = $this->createMock(Environment::class);

        /**
         * FAKING Methods
         * ---------------
         */

        $this->mailerManager
            ->method('send')
            ->willReturn(null);

        $translator
            ->method('trans')
            ->willReturn('Fake Message');

        $twig
            ->method('render')
            ->willReturn('Fake Content');

        /**
         * Creating Object from FAKE injections
         * ------------------------------------
         */
        $this->usermanager = new UserManager(
            $this->passwordEncoder,
            $this->entityManager,
            $translator,
            $twig,
            $requestStack,
            $this->mailerManager,
            $this->createMock(FlashBagInterface::class),
            new UserChecker()
        );


    }

    /*####################
     # UserManager TESTS #
     ####################*/

    /*---------
    | REGISTER |
    -----------*/

    public function testRegister()
    {
        // INIT
        //-----

        $this->passwordEncoder
            ->method('encodePassword')
            ->willReturn($this->user->getPassword());


        // TEST
        //-----

        // Check if all is ok
        $this->assertTrue($this->usermanager->register($this->user));

        // Check if user is disabled
        $this->assertNotTrue($this->user->isEnabled());

        // Test if a token has been added
        $this->assertNotNull($this->user->getToken());

        // Test if no validi
        $this->assertNull($this->user->getTokenValidity());
    }


    public function testRegisterWithError()
    {
        // INIT
        //-----

        $this->user->setEnabled();

        $this->passwordEncoder
            ->method('encodePassword')
            ->willReturn($this->user->getPassword());

        $this->mailerManager
            ->method('send')
            ->will($this->throwException(new \Exception()));

        // TEST
        //-----

        // Check if all is not ok
        $this->assertFalse($this->usermanager->register($this->user));
    }

    /*--------------------
    | REGISTER VALIDATION |
    ---------------------*/

    public function testRegisterValidation()
    {
        // INIT
        //-----

        $this
            ->user
            ->setDisabled()
            ->setToken();


        // TEST
        //-----

        // Check if all is ok
        $this->assertTrue($this->usermanager->registerValidation());

        // Check if user is enabled
        $this->assertTrue($this->user->isEnabled());

        // Test if a token has been removed
        $this->assertNull($this->user->getToken());

        // Test if no validity
        $this->assertNull($this->user->getTokenValidity());

    }

    public function testRegisterValidationWithError()
    {
        // INIT
        //-----

        // add an exception
        $this->entityManager
            ->method('persist')
            ->will($this->throwException(new \Exception()));

        // TEST
        //-----

        // Check if all is not ok
        $this->assertFalse($this->usermanager->registerValidation());
    }

    /*--------
    | RECOVER |
    ----------*/

    public function testRecoverPasswordDisabledUser()
    {

        $this->user->setDisabled();

        // TEST
        //-----

        // Check if all is ok
        $this->assertTrue($this->usermanager->recover('Fake Email'));


        // Check if user is not enabled
        $this->assertNotTrue($this->user->isEnabled());

        // Test if a token has been added
        $this->assertNotNull($this->user->getToken());

        // Test if no validity (as user not enabled)
        $this->assertNull($this->user->getTokenValidity());

    }

    public function testRecoverPasswordEnabledUser()
    {

        $this->user->removeToken()->setEnabled();

        // TEST
        //-----

        // Check if all is ok
        $this->assertTrue($this->usermanager->recover($this->user->getEmail()));


        // Check if user is enabled
        $this->assertTrue($this->user->isEnabled());

        // Test if a token has been added
        $this->assertNotNull($this->user->getToken());

        // Test if has validity (as user is enabled)
        $this->assertNotNull($this->user->getTokenValidity());

    }


    public function testRecoverPasswordWithError()
    {

        // INIT
        //-----

        $this->user->setEnabled();

        $this->mailerManager
            ->method('send')
            ->will($this->throwException(new \Exception()));

        // TEST
        //-----

        // Check if all is not ok
        $this->assertFalse($this->usermanager->recover($this->user->getEmail()));

    }

    /*-------------------
    | RECOVER VALIDATION |
    ---------------------*/

    public function testRecoverValidation()
    {
        // INIT
        //-----

        $this
            ->user
            ->setDisabled()
            ->setToken();

        $this->passwordEncoder
            ->method('encodePassword')
            ->willReturn($this->user->getPassword());

        // TEST
        //-----

        // Check if all is ok

        $this->assertTrue($this->usermanager->recoverValidation($this->user));

        // Check if user is enabled
        $this->assertTrue($this->user->isEnabled());

        // Test if a token has been removed
        $this->assertNull($this->user->getToken());

        // Test if no validity
        $this->assertNull($this->user->getTokenValidity());
    }

    public function testRecoverValidationWithError()
    {
        // INIT
        //-----

        $this->passwordEncoder
            ->method('encodePassword')
            ->will($this->throwException(new \Exception()));

        // TEST
        //-----

        // Check if all is not ok
        $this->assertFalse($this->usermanager->recoverValidation($this->user));
    }

    /*#############
     # UTIL FUNCS #
     #############*/

    /**
     * @param string $param
     * @return null|string
     */
    public function getRequestCallBack(string $param) : ? string
    {
        switch($param){
            case 'email' : return $this->user->getEmail();
            case 'token' : return $this->user->getToken();
        }

        return null;
    }
}
