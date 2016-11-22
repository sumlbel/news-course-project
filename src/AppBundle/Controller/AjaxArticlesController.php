<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AjaxArticlesController extends Controller
{
    public function articlesListAction(Request $request)
    {
        $users = $this->_getRequestedArticles($request);
        $array = array();
        foreach ($users as $user) {
            $array = $this->_serialize($array, $user);
        }
        return $this->json($array);
    }

    private function _filterField($filterField, $pattern){
        if ($filterField != null) {
            return array(
                $filterField => $pattern);
        } else {
            return array();
        }
    }

    /**
     * @param $array
     * @param Article $article
     * @return array
     */
    private function _serialize($array, Article $article)
    {
        $array = array_merge(
            $array,
            array(
                $article->getId() =>
                    array(
                        'id' => $article->getId(),
                        'title' => $article->getTitle(),
                        'category' => $article->getCategory(),
                        'publication date' => $article->getPublicationDate(),
                        'description' => $article->getDescription()
                    )
            )
        );

        return $array;
    }

    /**
     * @param Request $request
     * @return \AppBundle\Entity\Article[]|array
     */
    private function _getRequestedArticles(Request $request)
    {
        $usersPerPage = 15;
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $sortByField = $request->query->get('sortbyfield', 'title');
        $order = $request->query->get('order', 'asc');
        $filterField = $request->query->get('filterbyfield');
        $pattern = $request->query->get('pattern');
        $page = $request->query->get('page', 1) - 1;
        $users = $repository->findBy(
            $this->_filterField($filterField, $pattern),
            array(
                $sortByField => $order
            ),
            $usersPerPage,
            $page * $usersPerPage
        );

        return $users;
    }
}
