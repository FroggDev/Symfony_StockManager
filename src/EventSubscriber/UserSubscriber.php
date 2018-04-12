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

use App\Common\Traits\Client\UserTrait;
use App\Entity\User;
use App\Exception\Account\AccountAccessDeniedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * @author Frogg <admin@frogg.fr>
 */
class UserSubscriber implements EventSubscriberInterface
{

    use UserTrait;

    /** @var EntityManagerInterface */
    private $eManager;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var User */
    private $user;

    /**
     * LoginSubscriber constructor.
     * @param EntityManagerInterface   $eManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManagerInterface $eManager, EventDispatcherInterface $dispatcher)
    {
        $this->eManager = $eManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Update user last connexion field when login
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the User entity.
        $this->user = $event->getAuthenticationToken()->getUser();

        if(!$this->user->isEnabled()){
            throw new AccountAccessDeniedException();
        }

        if ($this->user instanceof User) {
            // set last connexion
            $this->user->setLastConnexion();
            //save to database
            $this->eManager->flush();
            //add event for the reponse
            $this->dispatcher->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse']);
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
         $event->getResponse()->headers->setCookie($this->getUserCookie($this->user->getEmail()));
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
        return [SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin'];
    }
}
