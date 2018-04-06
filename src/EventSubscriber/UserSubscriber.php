<?php
namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Class UserSubscriber
 * @package App\Subscriber
 */
class UserSubscriber implements EventSubscriberInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $eManager;

    /**
     * LoginSubscriber constructor.
     * @param EntityManagerInterface $eManager
     */
    public function __construct(EntityManagerInterface $eManager)
    {
        $this->eManager = $eManager;
    }

    /**
     * Update user last connexion field when login
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        # Get the User entity.
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof User) {
            # set last connexion
            $user->setLastConnexion();
            # save to database
            $this->eManager->flush();
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
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin'
        ];
    }
}
