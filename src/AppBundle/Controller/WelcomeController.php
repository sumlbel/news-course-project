<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WelcomeController extends Controller
{
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getEntityManager()->getRepository(
            'AppBundle:Article');
        $articles = $repository->findAllArticles();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            3/*limit per page*/
        );

        // parameters to template
        return $this->render(
            'default/index.html.twig',
            array('pagination' => $pagination));
    }
}
