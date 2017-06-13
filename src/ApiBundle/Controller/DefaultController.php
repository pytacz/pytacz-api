<?php

namespace ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;

class DefaultController
{
    /**
     * @Rest\View
     */
    public function indexAction()
    {
        $data = ['test' => 'ok'];
        return $data;
    }
}