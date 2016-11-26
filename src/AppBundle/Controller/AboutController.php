<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller
{
    public function indexAction()
    {
        return $this->render('about/about.html.twig');
    }
}
