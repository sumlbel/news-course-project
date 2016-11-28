<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\NewsForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends Controller
{

    /**
     * Displays a form to edit an existing article entity.
     *
     * @Route("/article/", name="article")
     *
     */
    public function indexAction()
    {
        return $this->render('administration/moderator.html.twig');
    }

    /**
     * Displays a form to edit an existing article entity.
     *
     * @Route("/article/edit/{id}", name="article_edit")
     *
     */
    public function editAction(Request $request, $id)
    {
        if ($id === 'new') {
            $article = new Article();
        } else {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
            $article = $repository->findOneBy(array('id' => $id));
        }

        $form = $this->createForm(NewsForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
            if ($id !== 'new') {
                return $this->redirectToRoute('article', array('id' => $id));
            } else {
                return $this->redirectToRoute('edit_article', array('id' => $id));
            }
        }

        return $this->render(
            'news/article_edit.html.twig', array(
            'form' => $form->createView()
            )
        );
    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("/{_locale}/article/{id}", name="article_show",
     *     requirements={"id": "\d+", "_locale": "en|ru|be"})
     *
     */
    public function showAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findOneBy(array('id' => $id));
        $article->setViews($article->getViews()+1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        return $this->render(
            'news/article_page.html.twig', array(
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'author' => $article->getAuthor(),
                'category' => $article->getCategory()->getName(),
                'publicationDate' => $article->getPublicationDate(),
                'views' => $article->getViews(),
                'body' => $article->getBody(),
                'similarArticles' => $article->getSimilarArticles(),
            )
        );
    }

    /**
     * Deletes a article entity.
     *
     * @Route("/article/delete/{id}", name="article_delete")
     *
     */
    public function deleteAction($id)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Article');
        $article = $repository->findOneBy(array('id' => $id));
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        return $this->redirectToRoute('main');
    }
}
