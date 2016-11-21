<?php

namespace AppBundle\Controller;

use AppBundle\Form\RegistrationForm;
use AppBundle\Util\TokenGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->_setEncryptPassword($user);
            $this->_sendConfirmationInfo($user);
            $this->addFlash(
                'confirmation-email-sent',
                'We sent you a verify link. Please check your email.'
            );
        } elseif ($form->isSubmitted()) {
            $this->addFlash(
                'form-is-invalid',
                'Form is invalid. Please check entered information'
            );
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function confirmAction($token)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->_findUserByToken($token);
        $user->setIsActive(true);
        $user->setConfirmationToken(null);
        $em->persist($user);
        $em->flush();
        $this->addFlash(
            'can-login',
            'All right. Now you can log in'
        );

        return $this->redirectToRoute('login');
    }

    private function _setEncryptPassword(User $user)
    {
        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
    }

    private function _sendConfirmationInfo(User $user)
    {
        $url = $this->_generateConfirmationInfo($user);

        $message = \Swift_Message::newInstance()
            ->setSubject('Verify your account on Newscast')
            ->setFrom('verify@newscast.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'registration/confiramtionEmail.txt.twig',
                    array('username' => $user->getUsername(),
                        'confirmationUrl' => $url
                    )
                )
            );
        $this->get('mailer')->send($message);
    }

    private function _generateConfirmationInfo(User $user)
    {
        $tokenGenerator = new TokenGenerator();
        $em = $this->getDoctrine()->getManager();
        $user->setConfirmationToken($tokenGenerator->generateToken());
        $em->flush();
        $url = $this->generateUrl(
            'register_confirmation',
            array('token' => $user->getConfirmationToken()),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $url;
    }

    private function _findUserByToken($token)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->findOneBy(array('confirmationToken' => $token));
        if (null === $user) {
            throw new NotFoundHttpException(
                sprintf(
                    'The user with "token" does not exist for value "%s"',
                    $token
                )
            );
        }

        return $user;
    }
}
