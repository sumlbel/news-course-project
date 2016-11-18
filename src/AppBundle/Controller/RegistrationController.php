<?php

namespace AppBundle\Controller;

use AppBundle\Form\RegistrationForm;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->_setEncryptPassword($user);
            return $this->redirectToRoute('login');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }

    private function _setEncryptPassword($user)
    {
        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        // 4) save the User!
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }
}
