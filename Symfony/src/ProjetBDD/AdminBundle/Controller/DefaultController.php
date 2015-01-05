<?php

/**
 * @file DefaultController.php
 * @brief Controleur d'affichage de liste de concept et de terme pour la page d'accueil des administration pour chacun d'entre eux.
 * @author Brice V.
 * @class DefaultController
 */

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
        //asort($tabConcept);
        return $this->render('ProjetBDDAdminBundle:Default:indexConcept.html.twig', array('tabConcept' => $tabConcept));
    }

    public function indexTermeAction()
    {
        $crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');

        $tabTerme = $crudTerme->getAll();
        //asort($tabTerme);
        return $this->render('ProjetBDDAdminBundle:Default:indexTerme.html.twig', array('tabTerme' => $tabTerme));
    }
}
