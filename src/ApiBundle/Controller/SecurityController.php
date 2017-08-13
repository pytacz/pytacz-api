<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(): array
    {
        throw new \Exception('This should not be reached');
    }

    public function logoutAction()
    {
        throw new \Exception("This should not be reached");
    }
}
