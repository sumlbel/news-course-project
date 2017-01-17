<?php

namespace AppBundle\Controller;

use Elastica\Query;
use Elastica\Query\Fuzzy;
use Elastica\Query\QueryString;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MainPageController extends Controller
{
    const ARTICLES_PER_PAGE = 8;
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
            self::ARTICLES_PER_PAGE
        );

        return $this->render(
            'default/main.html.twig',
            ['pagination' => $pagination]
        );
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
            self::ARTICLES_PER_PAGE
        );
        $repositoryCategory = $this->getDoctrine()->getRepository(
            'AppBundle:Category'
        );
        $categoryName = $repositoryCategory
            ->findOneBy(['id' => $id])->getName();

        return $this->render(
            'default/category.html.twig',
            ['pagination' => $pagination,
                'categoryName' => $categoryName]
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
        $finder = $this->get('fos_elastica.finder.app.article');
        $search = $request->get('_search');
        $paginator = $this->get('knp_paginator');

        $bodyQuery = new Fuzzy('body', $search);
        $results = $finder->createPaginatorAdapter($bodyQuery);
        $pagination = $paginator->paginate(
            $results,
            $request->query->getInt('page', 1),
            self::ARTICLES_PER_PAGE
        );

        return $this->render(
            'default/search.html.twig',
            ['pagination' => $pagination]
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
            'main',
            ['_locale' => $request->getLocale()]
        );
    }
}
