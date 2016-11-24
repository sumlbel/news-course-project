<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\NewsForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ArticleEditController extends Controller
{
    public function creationAction(Request $request)
    {
        $article = new Article();

        $form = $this->createForm(NewsForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            return $this->redirectToRoute('create_article');
        }

        return $this->render(
            'news/articleEdit.html.twig', array(
            'form' => $form->createView()
            )
        );
    }
}
