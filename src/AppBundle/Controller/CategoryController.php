<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category controller.
 *
 * @Route("{_locale}/category", requirements={"_locale": "en|ru|be"})
 */
class CategoryController extends Controller
{
    /**
     * Lists all category entities.
     *
     * @Route("/",    name="category_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $categories = $em->getRepository('AppBundle:Category')->findAll();
        return $this->render(
            'category/index.html.twig', array(
                'categories' => $categories
            )
        );
    }

    /**
     * Creates a new category entity.
     *
     * @Route("/new",  name="category_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $category = new Category();
        $form = $this->createForm('AppBundle\Form\CategoryType', $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute(
                'category_index', array('id' => $category->getId())
            );
        }

        return $this->render(
            'category/new.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
                )
        );
    }

    /**
     * Displays a form to edit an existing category entity.
     *
     * @Route("/edit/{id}", name="category_edit")
     * @Method({"GET",      "POST"})
     */
    public function editAction(Request $request, Category $category)
    {
        $editForm = $this->createForm('AppBundle\Form\CategoryType', $category);
        $editForm->handleRequest($request);
        $deleteForm = $this->createDeleteForm($category);
        $em = $this->getDoctrine()->getManager();
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute(
                'category_index'
            );
        }

        return $this->render('category/edit.html.twig', array(
            'category' => $category,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ));
    }

    /**
     * Deletes a category entity.
     *
     * @Route("/delete/{id}",   name="category_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Category $category)
    {
        $form = $this->createDeleteForm($category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $repository = $em->getRepository('AppBundle:Article');
            $articles = $repository->findBy(array('category' => $category->getId()));
            foreach ($articles as $article) {
                $em->remove($article);
            }
            $em->remove($category);
            $em->flush();
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * Creates a form to delete a category entity.
     *
     * @param Category $category The category entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Category $category)
    {
        return $this->createFormBuilder()
            ->setAction(
                $this->generateUrl(
                    'category_delete', array('id' => $category->getId())
                )
            )
            ->setMethod('DELETE')
            ->getForm();
    }
}
