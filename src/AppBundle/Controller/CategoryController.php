<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    public function indexAction(Request $request, $id)
    {
        $articlesPerPage = 5;
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findBy(array('category' => $id));
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
}
