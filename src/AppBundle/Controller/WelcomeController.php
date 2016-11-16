<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class WelcomeController extends Controller
{
    /**
     * @Route("/", name="welcome")
     */
    public function listAction()
    {
        return new Response(
            '<html><head><title>Welcome!</title></head>
            <body><h1>Lets get started!</h1><br>Hello!</body></html>'
        );
    }
}
