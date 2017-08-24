<?php

namespace ApiBundle\EventListeners;

use ApiBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

class AuthenticationListener implements EventSubscriberInterface
{
    /**
     * @var EntityManager $em
     */
    private $em;
    /**
     * @var RequestStack $requestStack
     */
    protected $requestStack;


    public function __construct(EntityManager $em, RequestStack $requestStack)
    {
        $this->em = $em;
        $this->requestStack = $requestStack;
    }

    /**
     * Sets last_date and last_ip after user request
     *
     * @param AuthenticationEvent $event
     *
     * @return void
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();
        $clientIp = $this->requestStack->getMasterRequest()->getClientIp();
        $user->setLastIp($clientIp);
        $user->setLastDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush($user);
    }

    public static function getSubscribedEvents()
    {
        return array(AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess');
    }
}
