<?php

namespace ApiBundle\EventListeners;

use ApiBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\RequestStack;

class AuthenticationListener
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
     * @param AuthenticationSuccessEvent $event
     *
     * @return void
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();
        $clientIp = $this->requestStack->getMasterRequest()->getClientIp();
        $user->setLastIp($clientIp);
        $user->setLastDate(new \DateTime());
        $this->em->persist($user);
        $this->em->flush($user);
    }

    /**
     * Custom message for authentication failure
     *
     * @param AuthenticationFailureEvent $event
     *
     * @return void
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse('Niepoprawne dane');

        $event->setResponse($response);
    }
}
