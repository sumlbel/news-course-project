<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NewsController extends Controller
{
    public function indexAction($slug)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findOneBy(array('id' => $slug));
        $article->setViews($article->getViews()+1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->render(
            'news/newsPage.html.twig', array(
                'title' => $article->getTitle(),
                'author' => $article->getAuthor(),
                'category' => $article->getCategory()->getName(),
                'publicationDate' => $article->getPublicationDate(),
                'views' => $article->getViews(),
                'body' => $article->getBody(),
            )
        );
    }
}
