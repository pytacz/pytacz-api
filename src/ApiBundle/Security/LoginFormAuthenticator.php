<?php

namespace ApiBundle\Security;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    /** @var FormFactoryInterface $formFactory */
    private $formFactory;
    /** @var EntityManager $em */
    private $em;
    /** @var EncoderFactory $encoderFactory */
    private $encoderFactory;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, EncoderFactory $encoderFactory)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
    }

    public function getCredentials(Request $request): ?array
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            //skip authentication
            return null;
        }

        $data = [
            '_username' => $request->request->get('_username'),
            '_password' => $request->request->get('_password'),
        ];

        $request->getSession()->set(Security::LAST_USERNAME, $data['_username']);

        return $data;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['_username'];

        $user = $this->em->getRepository('ApiBundle:User')
            ->findOneBy(['username' => $username]);

        if ($user) {
            return $user;
        }

        throw new CustomUserMessageAuthenticationException('Niepoprawne dane');
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];
        $username = $credentials['_username'];

        $encoder = $this->encoderFactory->getEncoder($user);

        $user = $this->em->getRepository('ApiBundle:User')
            ->findOneBy(['username' => $username]);

        $bool = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());

        if ($bool) {
            $user->setApiToken(substr(bin2hex(random_bytes(20)), 0, 20));
            $this->em->flush($user);
            return true;
        }

        throw new CustomUserMessageAuthenticationException('Niepoprawne dane');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'apiToken' => $token->getUser()->getApiToken()
        ], Response::HTTP_OK);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        if ($message == 'Account is disabled.') {
            $message = 'Konto nie jest aktywne';
        }

        return new JsonResponse([
            'success' => false,
            'message' => $message
        ], Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'Autentykacja wymagana
        '], Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
