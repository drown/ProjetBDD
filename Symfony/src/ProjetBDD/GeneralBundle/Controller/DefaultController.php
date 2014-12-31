<?php

namespace ProjetBDD\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Concept;

class DefaultController extends Controller
{
    public function indexAction()
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

    public function afficherTermeAction($nom) {
    	$crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
    	$terme = $crudTerme->getByNom($nom);

    	$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');
		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

    	if (isset($terme)) {
    		$tabAssoc = array();
    		$tabTraduit = array();
    		$tabSynonyme = array();
    		foreach ($terme->getAssocie() as $key => $value) {
    			$tabAssoc[] = $value;
    			$requete = oci_parse($connect, 'SELECT description FROM Terme WHERE nomTerme = :nomT');
				oci_bind_by_name($requete, ':nomT', $value);

				if (!$requete)
				{
					$e = oci_error();
					throw new \Exception('Erreur de requête : '. $e['message']);
				}

				$exe = oci_execute($requete);

				while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
				{	
					//Normalement qu'1 mais bon..
					$tabAssoc[] = $ligne['DESCRIPTION'];
				}

    		}
    		foreach ($terme->getTraduit() as $key => $value) {
    			$tabTraduit[] = $value;
    			$requete = oci_parse($connect, 'SELECT description FROM Terme WHERE nomTerme = :nomT');
				oci_bind_by_name($requete, ':nomT', $value);

				if (!$requete)
				{
					$e = oci_error();
					throw new \Exception('Erreur de requête : '. $e['message']);
				}

				$exe = oci_execute($requete);

				while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
				{	
					//Normalement qu'1 mais bon..
					$tabTraduit[] = $ligne['DESCRIPTION'];
				}
    		}

    		foreach ($terme->getSynonyme() as $key => $value) {
    			$tabSynonyme[] = $value;
    			$requete = oci_parse($connect, 'SELECT description FROM TermeVedette WHERE nomTermeVedette = :nomT');
    			oci_bind_by_name($requete, ':nomT', $value);

				if (!$requete)
				{
					$e = oci_error();
					throw new \Exception('Erreur de requête : '. $e['message']);
				}

				$exe = oci_execute($requete);

				while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
				{	
					//Normalement qu'1 mais bon..
					$tabSynonyme[] = $ligne['DESCRIPTION'];
				}
    		}

    		//Il manque synonymes.
    		return $this->render('ProjetBDDGeneralBundle:Default:affiche.html.twig', array('nomTerme' => $terme->getNomTerme(),
    																						'descTerme' => $terme->getDescrition(),
    																						'tabAssoc' => $tabAssoc,
    																						'tabTraduit' => $tabTraduit,
    																						'tabSynonyme'=> $tabSynonyme);
    		oci_free_statement($requete);
    	}
    	else {
    		return $this->render('ProjetBDDGeneralBundle:Default:index.html.twig', array('error' => 'Aucun terme correspond à votre recherche');
    	}

    	oci_close($connect);
    }

    public function afficherConceptAction($nom) {
    	//On recupere l'objet associé
    	$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
    	$concept = $crudConcept->getByNom($nom);
    	if (isset($result)) {
    		//On va aller dans la BDD, recuperer le termevedette associé a ce concept.
    		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

			if (!$connect)
			{
				$e = oci_error();
				throw new \Exception('Erreur de connexion : '. $e['message']);
			}

			$requete = oci_parse($connect, 'SELECT nomTerme, description FROM TermeVedette t WHERE DEREF(concept).nomConcept = :nomC ');
			oci_bind_by_name($requete, ':nomC', $concept->getNomConcept());

			if (!$requete)
			{
				$e = oci_error();
				throw new \Exception('Erreur de requête : '. $e['message']);
			}

			$exe = oci_execute($requete);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}

			while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
			{
				//Normalement qu'une seule fois
				$nomTermeVedette = $ligne['NOMCONCEPT'];
				$descTermeVedette = $ligne['DESCRIPTION'];

				$tabAssoc = array();
				$requeteAssoc = oci_parse($connect, 'SELECT nomTerme, description FROM Terme WHERE DEREF(VALUE(associe)).nomTerme = :nomT');
				oci_bind_by_name($requeteAssoc, ':nomT', $nomTermeVedette);
				if (!$requeteAssoc)
				{
					$e = oci_error();
					throw new \Exception('Erreur de requête : '. $e['message']);
				}

				$exe = oci_execute($requeteAssoc);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				while (($ligneAssoc = oci_fetch_array($requeteAssoc, OCI_ASSOC)))
				{
					$tabAssoc[] = $ligneAssoc['NOMTERME'];
					$tabAssoc[] = $ligneAssoc['DESCRIPTION'];
				}


				$tabTraduit = array();
				$requeteAssoc = oci_parse($connect, 'SELECT nomTerme, description FROM Terme WHERE DEREF(VALUE(traduit)).nomTerme = :nomT');
				oci_bind_by_name($requeteAssoc, ':nomT', $nomTermeVedette);
				if (!$requeteAssoc)
				{
					$e = oci_error();
					throw new \Exception('Erreur de requête : '. $e['message']);
				}

				$exe = oci_execute($requeteAssoc);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				while (($ligneAssoc = oci_fetch_array($requeteAssoc, OCI_ASSOC)))
				{
					$tabTraduit[] = $ligneAssoc['NOMTERME'];
					$tabTraduit[] = $ligneAssoc['DESCRIPTION'];
				}

				$tabSynonyme = array();
				$requeteAssoc = oci_parse($connect, 'SELECT nomTerme, description FROM Terme WHERE DEREF(VALUE(synonyme)).nomTerme = :nomT');
				oci_bind_by_name($requeteAssoc, ':nomT', $nomTermeVedette);
				if (!$requeteAssoc)
				{
					$e = oci_error();
					throw new \Exception('Erreur de requête : '. $e['message']);
				}

				$exe = oci_execute($requeteAssoc);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				while (($ligneAssoc = oci_fetch_array($requeteAssoc, OCI_ASSOC)))
				{
					$tabSynonyme[] = $ligneAssoc['NOMTERME'];
					$tabSynonyme[] = $ligneAssoc['DESCRIPTION'];
				}	
			}
			oci_free_statement($requeteAssoc);
			oci_close($connect);
			//Il manque le tab synonymes.
			return $this->render('ProjetBDDGeneralBundle:Default:affiche.html.twig', array('nomConcept' => $concept->getNomConcept(),
																							'descConcept' => $concept->getDescription(),
																							'nomTermeVedette' => $nomTermeVedette,
																							'descTermeVedette' => $descTermeVedette,
																							'tabAssocie' => $tabAssocie,
																							'tabTraduit' => $tabTraduit,
																							'tabSynonyme' => $tabSynonyme);


    	} else {
    		return $this->render('ProjetBDDGeneralBundle:Default:index.html.twig', array('error' => 'Aucun concept correspond à votre recherche');
    	}
    }	
}
