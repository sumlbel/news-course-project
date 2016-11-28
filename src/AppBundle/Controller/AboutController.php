<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutController extends Controller
{
    /**
    *
     *
    * @Route("{_locale}/about", name="about", requirements={"_locale": "en|ru|be"})
    */
    public function indexAction()
    {
        return $this->render('about/about.html.twig');
    }
}
