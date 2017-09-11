<?php

namespace ApiBundle\EventListeners;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CorsListener
{
    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            $response = $this->setResponseHeaders($response);
            $event->setResponse($response);
            return;
        }
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $response = $this->setResponseHeaders($response);
        $event->setResponse($response);
    }

    /**
     * @param Response $response
     * @return Response
     */
    private function setResponseHeaders(Response $response): Response
    {
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, PATCH, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Access-Control-Allow-Origin, Authorization, Content-Type');
        return $response;
    }
}
