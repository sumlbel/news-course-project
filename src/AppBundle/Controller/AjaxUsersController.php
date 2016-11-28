<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxUsersController extends Controller
{
    /**
     *
     *
     * @Route("/ajax/users", name="ajaxUsers")
     */
    public function usersListAction(Request $request)
    {
        $users = $this->_getRequestedUsers($request);
        $arrayCollection = $this->_serialize($request, $users);
        return new JsonResponse($arrayCollection);
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
     * @param User $user
     * @return array
     */
    private function _serialize(Request $request, $users)
    {
        $page = $request->query->get('page', 0);
        $usersPerPage = $request->query->get('rows', 15);
        $count = count($users);
        $start = $usersPerPage * $page;
        $end = $usersPerPage * ($page + 1);
        $end = ($end >= $count)?$count:$end;
        $arrayData = array();
        for ($i=$start; $i<$end && $i<$count;$i++) {
            $arrayData[] = array(
                'id' => $users[$i]->getId(),
                'username' => $users[$i]->getUsername(),
                'email' => $users[$i]->getEmail(),
                'isActive' => $users[$i]->getIsActive(),
                'role' => $users[$i]->getRoles()
            );
        }
        return array('rows' => $count, 'data' => $arrayData);
    }

    /**
     * @param Request $request
     * @return \AppBundle\Entity\User[]|array
     */
    private function _getRequestedUsers(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        $sortByField = $request->query->get('sortbyfield', 'username');
        $order = $request->query->get('order', 'asc');
        $pattern = $request->query->get('pattern');
        $filterField = $request->query->get('filterbyfield');
        $users = $repository->findBy(
            $this->_filterField($filterField, $pattern),
            array(
                $sortByField => $order
            )
        );

        return $users;
    }
}
