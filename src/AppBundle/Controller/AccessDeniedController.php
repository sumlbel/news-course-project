<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AccessDeniedController extends Controller
{
    public function indexAction()
    {
        return $this->render('security/accessDenied.html.twig');
    }
}
