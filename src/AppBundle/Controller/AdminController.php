<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    /**
     * @Route("/admin")
     */
    public function adminAction()
    {
        return new Response('<html><body><h1>Admin page!</h1><br>Hello!</body></html>');
    }
}
