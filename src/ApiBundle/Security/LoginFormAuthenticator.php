<?php

namespace ApiBundle\Security;

use Doctrine\ORM\EntityManager;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
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
    /** @var JWTEncoderInterface $jwtEncoder */
    private $jwtEncoder;

    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, EncoderFactory $encoderFactory, JWTEncoderInterface $jwtEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->encoderFactory = $encoderFactory;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Get authentication credentials from request
     *
     * @param   Request     $request
     *
     * @return  array       credentials
     */
    public function getCredentials(Request $request): array
    {
        $data = [
            '_username' => $request->request->get('_username'),
            '_password' => $request->request->get('_password'),
        ];

        return $data;
    }

    /**
     * Fetch user from database and check if exists
     *
     * @param   array                   $credentials       password and username
     * @param   UserProviderInterface   $userProvider
     *
     * @return  object or exception
     */
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

    /**
     * Check if password is correct
     *
     * @param   array                   $credentials       password and username
     * @param   UserInterface           $user
     *
     * @return  boolean or exception
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['_password'];

        $encoder = $this->encoderFactory->getEncoder($user);

        $bool = $encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt());

        if ($bool) {
            return true;
        }

        throw new CustomUserMessageAuthenticationException('Niepoprawne dane');
    }

    /**
     * Authentication success
     *
     * @param   Request         $request
     * @param   TokenInterface  $token
     * @param   string          $providerKey    login path
     *
     * @return  JsonResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): JsonResponse
    {
        $rememberMe = $request->request->get('_remember_me');

        if (!$rememberMe) {
            $expTime = time() + 3600; // 1 hour expiration
        } else {
            $expTime = time() + 31556929; // 1 year expiration
        }

        $token = $this->jwtEncoder
            ->encode([
                'username' => $request->request->get('_username'),
                'exp' => $expTime
            ]);

        return new JsonResponse([
            'token' => $token
        ], 200);
    }

    /**
     * Authentication failure
     *
     * @param   Request                     $request
     * @param   AuthenticationException     $exception
     *
     * @return  JsonResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        if ($message == 'Account is disabled.') {
            $message = 'Konto nie jest aktywne';
        }

        return new JsonResponse([
            'success' => false,
            'message' => $message
        ], 403);
    }

    /**
     * No authentication details were sent
     *
     * @param   Request                   $request
     * @param   AuthenticationException   $authException
     *
     * @return  JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'message' => 'Autentykacja wymagana'
        ], 401);
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
