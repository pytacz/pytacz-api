<?php

namespace ApiBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;

class RefreshToken
{
    /**
     * @var JWTEncoderInterface $jwtEncoder
     */
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Create a client with valid Authorization header.
     *
     * @param Request $request
     *
     * @return false or string
     */
    public function refreshToken(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if ($token) {
            $error = false;
            try {
                $this->jwtEncoder->decode($token);
            } catch (\Exception $e) {
                $error = $e->getMessage();
            }

            if (strcmp($error, 'Expired JWT Token') == 0 || !$error) {
                $pieces = explode('.', $token);

                $payload = json_decode(base64_decode($pieces[1]), true);

                $token = $this->jwtEncoder->encode([
                    'roles' => $payload['roles'],
                    'email' => $payload['email']
                ]);

                return $token;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
