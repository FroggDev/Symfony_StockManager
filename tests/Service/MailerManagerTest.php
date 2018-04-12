<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Service;

use App\Service\MailerManager;
use Swift_RfcComplianceException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @author Frogg <admin@frogg.fr>
 */
class MailerManagerTest extends KernelTestCase
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

    /** @var \Swift_Mailer */
    private $swiftmailer;

    public function setUp()
    {
        // fake $swiftmailer
        $this->swiftmailer = $this->getMockBuilder(\Swift_Mailer::class)
            ->disableOriginalConstructor()
            ->setMethods(array('send'))
            ->getMock();
        $this->swiftmailer
            ->method('send')
            ->will($this->returnValue(1));


    }

    /**
     * Test with all ok
     */
    public function testManagerTest()
    {
        // INIT
        //-----

        $mailManager = new MailerManager($this->swiftmailer);

        // TEST
        //-----

        //test return code
        $this->assertEquals(
            1,
            $mailManager->send(
                'test@frogg.fr',
                'test@frogg.fr',
                'bodyhtml',
                'bodytxt',
                'subject'
            )
        );
    }

    /**
     * Test with a bad mail
     */
    public function testManagerTestbadMail()
    {
        // INIT
        //-----

        $mailManager = new MailerManager($this->swiftmailer);

        // TEST
        //-----

        $this->expectException(Swift_RfcComplianceException::class);

        //test exception
        $mailManager->send(
            'from',
            'to',
            'bodyhtml',
            'bodytxt',
            'subject');
    }
}
