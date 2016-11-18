<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class WelcomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
