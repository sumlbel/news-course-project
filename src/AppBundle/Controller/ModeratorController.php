<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ModeratorController extends Controller
{
    public function moderatingArticlesAction()
    {
        return $this->render('administration/moderator.html.twig');
    }
}
