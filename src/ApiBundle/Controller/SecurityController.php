<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction()
    {
        throw new \Exception("This should not be reached");
    }

    public function refreshTokenAction(Request $request)
    {
        $result = $this->get('api.security.refresh_token')->refreshToken($request);

        if (!$result) {
            return new JsonResponse([
                'success' => false
            ], 401);
        } else {
            return new JsonResponse([
                'token' => $result
            ], 200);
        }
    }
}