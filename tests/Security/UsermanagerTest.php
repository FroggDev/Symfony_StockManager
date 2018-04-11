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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UsermanagerTest extends KernelTestCase
{

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserManager */
    private $usermanager;

    /** @var MailerManager */
    private $mailerManager;

    /*#######################
     # ONCE BEFORE EACH TEST #
     #######################*/

    public function setUp()
    {

        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->mailerManager = $this->createMock(MailerManager::class);

        $translator = $this->createMock(TranslatorInterface::class);

        $twig = $this->createMock(Environment::class);

        $this->usermanager = new UserManager(
            $this->passwordEncoder,
            $this->createMock(EntityManagerInterface::class),
            $translator,
            $twig,
            $this->createMock(RequestStack::class),
            $this->mailerManager,
            $this->createMock(FlashBagInterface::class),
            new UserChecker()
        );

        $this->mailerManager
            ->method('send')
            ->willReturn(null);

        $translator
            ->method('trans')
            ->willReturn('Fake Message');

        $twig
            ->method('render')
            ->willReturn('Fake Content');
    }

    /*####################
     # UserManager TESTS #
     ####################*/

    public function testRegisterAUser()
    {
        // INIT
        //-----

        $user = new User();

        $user
            ->setPassword('Fake Password')
            ->setEmail('Fake Email')
            ->setEnabled();

        $this->passwordEncoder
            ->method('encodePassword')
            ->willReturn($user->getPassword());


        // TEST
        //-----

        // Check if all is ok
        $this->assertTrue($this->usermanager->register($user));

        // Check if user is disabled
        $this->assertNotTrue($user->isEnabled());

        // Test if a token has been added
        $this->assertNotNull($user->getToken());

        // Test if no validi
        $this->assertNull($user->getTokenValidity());
    }


    public function testRegisterAUserWithError()
    {
        // INIT
        //-----

        $user = new User();

        $user
            ->setPassword('Fake Password')
            ->setEmail('Fake Email')
            ->setEnabled();

        $this->passwordEncoder
            ->method('encodePassword')
            ->willReturn($user->getPassword());

        $this->mailerManager
            ->method('send')
            ->will($this->throwException(new \Exception()));

        // TEST
        //-----

        // Check if all is not ok
        $this->assertFalse($this->usermanager->register($user));
    }
}
