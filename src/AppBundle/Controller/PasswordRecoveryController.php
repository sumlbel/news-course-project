<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PasswordRecoveryController extends Controller
{
    /**
     * @Route("/forgot_password", name="forgot")
     */
    public function recoveryAction(Request $request)
    {
        $email = $request->request->get('_email');
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->findOneBy(array('email' => $email));
        if ($user) {
            $this->_sendRecoveryInfo($user);
            $this->addFlash(
                'recovery-password-success',
                'All done, check your email for further instructions'
            );


        } elseif ($request->isMethod('POST')) {
            $this->addFlash(
                'recovery-password-failed',
                'There is no registered user with that Email.'
            );
        }

        return $this->render(
            'security/password_recovery.html.twig', array(
                'email' => $email
            )
        );
    }

    private function _sendRecoveryInfo(User $user)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject('Recovery your account on Newscast')
            ->setFrom('recovery@newscast.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'security/recoveryEmail.txt.twig',
                    array('username' => $user->getUsername(),
                        'password' => $user->getPassword()
                    )
                )
            );
        $this->get('mailer')->send($message);
    }
}
