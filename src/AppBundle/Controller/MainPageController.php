<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
define('ARTICLES_PER_PAGE', '8');
class MainPageController extends Controller
{
    /**
     *
     *
     * @Route("/{_locale}/", name="main", requirements={"_locale": "en|ru|be"})
     */
    public function noFilterAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $articles = $repository->findAllArticles();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            ARTICLES_PER_PAGE
        );

        return $this->render(
            'default/main.html.twig',
            array('pagination' => $pagination));
    }

    /**
     *
     *
     * @Route("/{_locale}/filter", name="category_show",
     *     requirements={"_locale": "en|ru|be"})
     */
    public function filterByCategoryAction(Request $request)
    {
        $id = $request->get('id');
        $repositoryArticles = $this->getDoctrine()->getRepository(
            'AppBundle:Article'
        );
        $articles = $repositoryArticles->findByCategory($id);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $articles,
            $request->query->getInt('page', 1),
            ARTICLES_PER_PAGE
        );
        $repositoryCategory = $this->getDoctrine()->getRepository(
            'AppBundle:Category'
        );
        $categoryName = $repositoryCategory->findOneBy(
            array('id' => $id)
        )->getName();

        return $this->render(
            'default/category.html.twig',
            array('pagination' => $pagination, 'categoryName' => $categoryName)
        );
    }

    /**
     *
     *
     * @Route("/{_locale}/search", name="search",
     *     requirements={"_locale": "en|ru|be"})
     */
    public function searchAction(Request $request)
    {
        $finder = $this->container->get('fos_elastica.finder.app.article');
        $search = $request->get('_search');
        $paginator = $this->get('knp_paginator');
        $results = $finder->createPaginatorAdapter($search);
        $pagination = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            ARTICLES_PER_PAGE
        );

        return $this->render(
            'default/main.html.twig',
            array('pagination' => $pagination)
        );
    }

    /**
     *
     *
     * @Route("/", name="main_default", requirements={"_locale": "en|ru|be"})
     */
    public function redirectToUsersLocaleAction(Request $request)
    {
        return $this->redirectToRoute(
            'main', array(
                '_locale' => $request->getLocale()
            )
        );
    }
}
