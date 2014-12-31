<?php

namespace ProjetBDD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ProjetBDDAdminBundle:Default:index.html.twig', array('name' => $name));
    }
}
