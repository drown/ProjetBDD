<?php

namespace ProjetBDD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Concept;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;

class DefaultController extends Controller
{
    public function indexConceptAction()
    {
        $crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');

        $tabConcept = $crudConcept->getAll();

        return $this->render('ProjetBDDAdminBundle:Default:indexConcept.html.twig', array('tabConcept' => $tabConcept));
    }

    public function indexTermeAction()
    {
        $crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');

        $tabTerme = $crudTerme->getAll();

        return $this->render('ProjetBDDAdminBundle:Default:indexTerme.html.twig', array('tabTerme' => $tabTerme));
    }
}
