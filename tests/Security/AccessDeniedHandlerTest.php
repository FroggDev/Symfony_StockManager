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

use App\Security\AccessDeniedHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Frogg <admin@frogg.fr>
 */
class AccessDeniedHandlerTest extends KernelTestCase
{
    /**
     * Test status
     */
    public function testIfReturnResponse() : void
    {

        // INIT
        //-----

        $this->createMock(Request::class);


        $controller = $this
            ->getMockBuilder( Controller::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMock();

        $controller
            ->method('render')
            ->with('security/access_denied.html.twig')
            ->will($this->returnValue(new \Symfony\Component\HttpFoundation\Response()));

        $request = $this
            ->getMockBuilder( Request::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $request
            ->method('get')
            ->with('router')
            ->will($this->returnValue($controller));


        $accesDeniedhandler = new AccessDeniedHandler();


        // TEST
        //-----

        $this->assertInstanceOf(
            'Symfony\Component\HttpFoundation\Response',
            $accesDeniedhandler->handle($request,new AccessDeniedException())
        );
    }

}