<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainPageController extends Controller
{
    public function indexAction(Request $request)
    {
        $articlesPerPage = 5;
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAllArticles();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            $articlesPerPage
        );

        // parameters to template
        return $this->render(
            'default/index.html.twig',
            array('pagination' => $pagination));
    }

    public function redirectToUsersLocaleAction(Request $request)
    {
        $request->getLocale();
        return $this->redirectToRoute(
            'main', array(
                '_locale' => $request->getLocale()
            )
        );
    }
}
