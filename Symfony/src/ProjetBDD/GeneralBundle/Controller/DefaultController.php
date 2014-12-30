<?php

namespace ProjetBDD\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Concept;

class DefaultController extends Controller
{
    public function indexAction(	)
    {
    	$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
    	$result = $crudConcept->findByNom('é');

        return $this->render('ProjetBDDGeneralBundle:Default:index.html.twig', array('result' => $result));
    }

    public function rechercheAction()
    {
    	
    }
}
