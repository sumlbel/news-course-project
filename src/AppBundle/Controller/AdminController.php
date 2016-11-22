<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdminController extends Controller
{
    public function adminAction($name)
    {
        return $this->render('administration/admin.html.twig');
    }
}
