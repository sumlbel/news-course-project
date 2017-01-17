<?php
/**
 * Created by PhpStorm.
 * User: sumlbel
 * Date: 26.11.16
 * Time: 8.54
 */

namespace AppBundle\Service;


use Doctrine\ORM\EntityManager;
use Knp\Menu\FactoryInterface;

class MenuBuilder
{
    private $factory;
    private $em;

    public function __construct(FactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public function mainMenu()
    {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');
        $menu->addChild(
            'category',
            ['label' => 'Categories']
        )->setAttribute('dropdown', true);

        $listCategories = $this->em->getRepository('AppBundle:Category')->findAll();

        foreach ($listCategories as $category) {
            $menu['category']->addChild(
                'category'.$category->getName(),
                ['label' => $category->getName(),
                    'route' => 'category_show',
                    'routeParameters' => ['id' => $category->getId()]
                ]
            )->setExtra('translation_domain', false);
        }

        return $menu;
    }
}
