<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxArticlesController extends Controller
{
    public function articlesListAction(Request $request)
    {
        $articles = $this->_getRequestedarticles($request);
        $arrayCollection = $this->_serialize($request, $articles);
        return new JsonResponse($arrayCollection);
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
    private function _serialize(Request $request, $articles)
    {
        $page = $request->query->get('page', 0);
        $articlesPerPage = $request->query->get('rows', 15);
        $count=count($articles);
        $start = $articlesPerPage * $page;
        $end = $articlesPerPage * ($page + 1);
        $end = ($end >= $count)?$count:$end;
        $arrayData = array();
        for ($i=$start; $i<$end;$i++) {
            $arrayData[] = array(
                'id' => $articles[$i]->getId(),
                'title' => $articles[$i]->getTitle(),
                'author' => $articles[$i]->getAuthor(),
                'category' => $articles[$i]->getCategory()->getName(),
                'publicationDate' =>
                    $articles[$i]->getPublicationDate()->format('d-M-Y')
            );
        }
        return array('rows' => $count, 'data' => $arrayData);
    }

    /**
     * @param Request $request
     * @return \AppBundle\Entity\Article[]|array
     */
    private function _getRequestedarticles(Request $request)
    {
        $repositoryArticle = $this->getDoctrine()->getRepository(
            'AppBundle:Article'
        );
        $repositoryCategory = $this->getDoctrine()->getRepository(
            'AppBundle:Category'
        );
        $sortByField = $request->query->get('sortbyfield', 'title');
        $order = $request->query->get('order', 'asc');
        $pattern = $request->query->get('pattern');
        $filterField = $request->query->get('filterbyfield');
        if ($filterField == 'category') {
            $pattern = $repositoryCategory->findOneBy(
                array('name' => $pattern)
            )->getId();
        }
        $articles = $repositoryArticle->findBy(
            $this->_filterField($filterField, $pattern),
            array(
                $sortByField => $order
            )
        );

        return $articles;
    }
}
