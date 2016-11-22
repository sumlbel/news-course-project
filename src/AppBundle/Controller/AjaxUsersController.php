<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AjaxUsersController extends Controller
{
    public function usersListAction(Request $request)
    {
        $users = $this->_getRequestedUsers($request);
        $array = array();
        foreach ($users as $user) {
            $array = $this->_serialize($array, $user);
        }
        return $this->json($array);
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
     * @param $user
     * @return array
     */
    private function _serialize($array, $user)
    {
        $array = array_merge(
            $array,
            array(
                $user->getId() =>
                    array(
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                        'email' => $user->getEmail(),
                        'is Active' => $user->getIsActive(),
                        'role' => $user->getRoles()
                    )
            )
        );

        return $array;
    }

    /**
     * @param Request $request
     * @return \AppBundle\Entity\User[]|array
     */
    private function _getRequestedUsers(Request $request)
    {
        $usersPerPage = 15;
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $sortByField = $request->query->get('sortbyfield', 'username');
        $order = $request->query->get('order', 'asc');
        $filterField = $request->query->get('filterbyfield');
        $pattern = $request->query->get('pattern');
        $page = $request->query->get('page', 1) - 1;
        $users = $repository->findBy(
            $this->_filterField($filterField, $pattern),
            array(
                $sortByField => $order
            ),
            $usersPerPage,
            $page * $usersPerPage
        );

        return $users;
    }
}
