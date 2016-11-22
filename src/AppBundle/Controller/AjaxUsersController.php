<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AjaxUsersController extends Controller
{
    public function usersListAction()
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $users = $repository->findAll();
        $array = array();
        foreach ($users as $user) {
            $array = array_merge(
                $array,
                array(
                    $user->getId()=>
                    array(
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'is Active' => $user->getIsActive(),
                        'role' => $user->getRoles()
                    )
                )
            );
        }
        return $this->json($array);
    }
}
