<?php
/*
 * This file is part of the StockManager.
 *
 * (c) Frogg <admin@frogg.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventSubscriber;

use App\Service\LocaleService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * @author Frogg <admin@frogg.fr>
 */
class LocaleSubscriber implements EventSubscriberInterface
{

    /** @var LocaleService */
    private $localeService;

    /**
     * LocaleSubscriber constructor.
     * @param LocaleService $localeService
     */
    public function __construct(LocaleService $localeService)
    {
        $this->localeService = $localeService;
    }


    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        //TEST PURPOSE
        //exit("onKernelController");
    }

    /**
     * Redirect user to his favorite language if
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent  $event)
    {
        $currentRoute = $event->getRequest()->get('_route');
        if ($event->isMasterRequest() && ( "index" === $currentRoute || "default" === $currentRoute)) {
            $this->localeService->changeDefaultLocale();
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER  => 'onKernelController',
            KernelEvents::REQUEST  => 'onKernelRequest',
        ];
    }
}
