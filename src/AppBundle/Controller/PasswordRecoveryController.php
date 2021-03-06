<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Util\TokenGenerator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class PasswordRecoveryController
 * @package AppBundle\Controller
 */
class PasswordRecoveryController extends Controller
{
    /**
     *
     *
     * @Route("/{_locale}/recovery", name="recovery",
     *     requirements={"_locale": "en|ru|be"})
     */
    public function recoveryAction(Request $request)
    {
        $email = $request->request->get('_email');
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->findOneBy(['email' => $email]);
        if ($user) {
            $this->_sendRecoveryInfo($user);
            $this->addFlash(
                'recovery-password-success',
                'We sent you email, check it for further instructions'
            );


        } elseif ($request->isMethod('POST')) {
            $this->addFlash(
                'recovery-password-failed',
                'There is no registered user with that Email.'
            );
        }

        return $this->render(
            'security/password_recovery.html.twig',
            ['email' => $email]
        );
    }

    /**
     *
     *
     * @Route("/{_locale}/reset/{token}", name="reset_password",
     *     requirements={"_locale": "en|ru|be"})
     */
    public function resetAction(Request $request, $token)
    {
        $user = $this->_findUserByToken($token);

        if ($request->isMethod('POST')) {
            $password = $request->request->get('_password');
            $repeatedPassword = $request->request->get('_repeatedPassword');
            if ($password === $repeatedPassword) {
                $this->_changePassword($user, $password);
                $this->addFlash(
                    'can-login',
                    'All right. Now you can log in'
                );
                return $this->redirectToRoute('login');
            } else {
                $this->addFlash(
                    'reset-password-failed',
                    'Passwords did not much'
                );
            }
        }

        return $this->render(
            'security/password_reset.html.twig',
            ['token' => $token]
        );
    }

    private function _sendRecoveryInfo(User $user)
    {
        $url = $this->_generateRecoveryInfo($user);

        $message = \Swift_Message::newInstance()
            ->setSubject('Recovery your account on Newscast')
            ->setFrom('recovery@newscast.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'security/recovery_email.txt.twig',
                    ['username' => $user->getUsername(),
                        'confirmationUrl' => $url]
                )
            );
        $this->get('mailer')->send($message);
    }

    private function _generateRecoveryInfo(User $user)
    {
        $tokenGenerator = new TokenGenerator();
        $em = $this->getDoctrine()->getManager();
        $user->setConfirmationToken($tokenGenerator->generateToken());
        $em->flush();
        $url = $this->generateUrl(
            'reset_password',
            ['token' => $user->getConfirmationToken()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $url;
    }

    private function _changePassword(User $user, $password)
    {
        $em = $this->getDoctrine()->getManager();
        $user->setPlainPassword($password);
        $password = $this->get('security.password_encoder')
            ->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $user->setConfirmationToken(null);
        $em->flush();
    }

    private function _findUserByToken($token)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repository->findOneBy(['confirmationToken' => $token]);
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
