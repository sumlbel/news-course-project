<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainPageController extends Controller
{
    public $articlesPerPage = 5;
    public function noFilterAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAllArticles();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            $this->articlesPerPage
        );

        return $this->render(
            'default/index.html.twig',
            array('pagination' => $pagination));
    }

    public function filterByCategoryAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findByCategory($id);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            $this->articlesPerPage
        );

        return $this->render(
            'default/index.html.twig',
            array('pagination' => $pagination));
    }

    public function searchAction(Request $request)
    {
        $finder = $request->get('fos_elastica.finder.app.user');
        $query = new Query();
        $search = $request->query->get('search');
        $search = $finder->find($query);
        $paginator = $this->get('knp_paginator');
        $results = $finder->createPaginationAdapter;
        $pagination = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            $this->articlesPerPage
        );

        return $this->render(
            'default/index.html.twig',
            array('pagination' => $pagination)
        );
    }

    public function redirectToUsersLocaleAction(Request $request)
    {
        return $this->redirectToRoute(
            'main', array(
                '_locale' => $request->getLocale()
            )
        );
    }
}
