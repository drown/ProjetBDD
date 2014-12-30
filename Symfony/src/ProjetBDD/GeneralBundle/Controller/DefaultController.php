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

    public function rechercheAction(Request $requete)
    {
    	//Erreur si pas de requete post ou aucun resultat
    	//Sinon 2 tableaux, tabConcept et tabTermes
    	$nom = $requete->request->get('nom');
    	if (isset($nom)) {

    		$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
    		$crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');

    		$tabConcept = array();
    		$tabTerme = array();

    		//Recuperation des tableaux 
    		$tabConcept = $crudConcept->findByNom($nom);
    		$tabTerme = $crudTerme->findByNom($nom);
    		//Ils peuvent etre vide, si oui erreur
    		if (count($tabConcept) > 0 || count($tabTerme) > 0) {
    			return $this->render('ProjetBDDGeneralBundle:Default:recherche.html.twig', array('tabConcept' => $tabConcept, 'tabTerme' => $tabTerme));
    		} else {
    			return $this->render('ProjetBDDGeneralBundle:Default:index.html.twig', array('error' => 'Aucun concept/terme correspond à votre recherche');
    		}
    		
    	}
    	else {
    		return $this->render('ProjetBDDGeneralBundle:Default:index.html.twig', array('error' => 'Aucun concept/terme correspond à votre recherche');
    	}
    }
}
