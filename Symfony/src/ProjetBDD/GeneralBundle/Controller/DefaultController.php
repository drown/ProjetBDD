<?php

namespace ProjetBDD\GeneralBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Concept;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;
use ProjetBDD\GeneralBundle\Entity\Terme;

class DefaultController extends Controller
{
    public function indexAction()
    {
    	$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
    	$result = $crudConcept->findByNom('é');

        return $this->render('ProjetBDDGeneralBundle:Default:index.html.twig', array('result' => $result));
    }

    public function rechercheAction()
    {
    	//Erreur si pas de requete post ou aucun resultat
    	//Sinon 2 tableaux, tabConcept et tabTermes
    	$request = $this->getRequest();

    	if ($request->getMethod() == 'POST')
        {
        	if (isset($_POST['nom'])) {
        		$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
        		$crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
        		$crudConcept = $crudConcept->findByNom($_POST['nom']);
        		$crudTerme = $crudTerme->findByNom($_POST['nom']);

        		return $this->render('ProjetBDDGeneralBundle:Default:recherche.html.twig', array('tabConcept' => $crudConcept, 'tabTerme' => $crudTerme, 'nom' => $_POST['nom']));
        	}
        }
        else {
        	return $this->render('ProjetBDDGeneralBundle:Default:recherche.html.twig', array());
        }
    }

    public function afficherTermeAction($nom) {
    	$crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
    	$terme = $crudTerme->getByNom($nom);

    	if (isset($nom)) {
    		$tabAssoc = array();
    		$tabTraduit = array();
    		$tabSynonyme = array();
    		
    		foreach ($terme->getAssocie() as $key => $value) {
				$termeTravail = $crudTerme->getByNom($value);
				if (isset($termeTravail)) {
					$tabAssoc[] = $termeTravail;
					$termeTravail = null;
				}
      		}
    		foreach ($terme->getTraduit() as $key => $value) {
    			$termeTravail = $crudTerme->getByNom($value);
				if (isset($termeTravail)) {
					$tabTraduit[] = $termeTravail;
					$termeTravail = null;
				}
    		}

    		foreach ($terme->getSynonymes() as $key => $value) {
    			$termeTravail = $crudTerme->getByNom($value);
				if (isset($termeTravail)) {
					$tabSynonyme[] = $termeTravail;
					$termeTravail = null;
				}
    		}

    		//Il manque synonymes.
    		return $this->render('ProjetBDDGeneralBundle:Default:afficheTerme.html.twig', array('nomTerme' => $terme->getNomTerme(),
    																						'descTerme' => $terme->getDescription(),
    																						'tabAssoc' => $tabAssoc,
    																						'tabTraduit' => $tabTraduit,
    																						'tabSynonyme'=> $tabSynonyme));
    		oci_free_statement($requete);
    	}
    	else {
    		return $this->render('ProjetBDDGeneralBundle:Default:afficheTerme.html.twig', array('error' => 'Aucun terme correspond à votre recherche'));
    	}

    	oci_close($connect);
    }

    public function afficherConceptAction($nom) {
    	//On recupere l'objet associé
    	$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
    	
    	if (isset($nom)) {
    		$concept = $crudConcept->getByNom($nom);
    		//On va aller dans la BDD, recuperer le termevedette associé a ce concept.
    		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

			if (!$connect)
			{
				$e = oci_error();
				throw new \Exception('Erreur de connexion : '. $e['message']);
			}

			$requete = oci_parse($connect, 'SELECT nomTerme, description FROM TermeVedette t WHERE DEREF(concept).nomConcept = :nomC ');
			oci_bind_by_name($requete, ':nomC', $nom);

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

				//TODO : un seul termevedette associé a un concept, right?
				$nomTermeVedette = $ligne['NOMTERME'];
				$descTermeVedette = $ligne['DESCRIPTION'];
				$terme = $this->container->get('ProjetBDD.CRUD.Terme');

				$tabAssoc = array();
				$requeteAssoc = oci_parse($connect, 'SELECT nomTerme FROM Terme t, Table (t.associe) t2 WHERE DEREF(VALUE(t2)).nomTerme = :nomT');
				oci_bind_by_name($requeteAssoc, ':nomT', $nom);
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
					
					$res = $terme->getByNom($ligneAssoc['NOMTERME']);
					$tabAssoc[] = $res;
				}


				$tabTraduit = array();
				$requeteAssoc = oci_parse($connect, 'SELECT nomTerme FROM Terme t, Table (t.traduit) t2 WHERE DEREF(VALUE(t2)).nomTerme = :nomT');
				oci_bind_by_name($requeteAssoc, ':nomT', $nom);
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
					$res = $terme->getByNom($ligneAssoc['NOMTERME']);
					$tabTraduit[] = $res;
				}

				$tabSynonyme = array();
				$requeteAssoc = oci_parse($connect, 'SELECT nomTerme FROM Terme t, Table (t.synonymes) t2 WHERE DEREF(VALUE(t2)).nomTerme = :nomT');
				oci_bind_by_name($requeteAssoc, ':nomT', $nom);
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
					$res = $terme->getByNom($ligneAssoc['NOMTERME']);
					$tabSynonyme[] = $res;
				}	
			}
			oci_free_statement($requeteAssoc);
			oci_close($connect);
			//Il manque le tab synonymes.
			return $this->render('ProjetBDDGeneralBundle:Default:afficheConcept.html.twig', array(
				'nomConcept' => $concept->getNomConcept(),'descConcept' => $concept->getDescription(),
				'nomTermeVedette' => $nomTermeVedette,'descTermeVedette' => $descTermeVedette,'tabAssocie' => $tabAssoc,
				'tabTraduit' => $tabTraduit,'tabSynonyme' => $tabSynonyme));


    	} else {
    		return $this->render('ProjetBDDGeneralBundle:Default:afficheConcept.html.twig', array('error' => 'Aucun concept correspond à votre recherche'));
    	}
    }	
}
