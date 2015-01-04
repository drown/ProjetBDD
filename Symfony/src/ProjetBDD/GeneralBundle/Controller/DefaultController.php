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
    	$crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');
    	$crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
    	$terme = $crudTerme->getByNom($nom);

    	if (isset($nom)) {
    		$connect = oci_connect('SYSTEM', 'Don699mute156', 'localhost/xe');
    		//On va rechercher le terme vedette, on remontera au concept grace a ca
			$requete = oci_parse($connect, 'SELECT nomTerme FROM TermeVedette t, Table(T.associe) t2 WHERE DEREF(VALUE(t2)).nomTerme = :nomT ');
			oci_bind_by_name($requete, ':nomT', $nom);

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
			$ligne = oci_fetch_array($requete, OCI_ASSOC);

			$TV = $crudTermeVedette->getByNom($ligne['NOMTERME']);

			$requete  = oci_parse($connect, 'SELECT deref(concept).nomConcept as NOMC FROM TermeVedette t WHERE nomTerme = :nomT');
			oci_bind_by_name($requete, ':nomT', $ligne['NOMTERME']);
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
			$ligne = oci_fetch_array($requete, OCI_ASSOC);

			$concept = $crudConcept->getByNom($ligne['NOMC']);


			$tabAssoc = $terme->getAssocie();
			$tabTrad = $terme->getTraduit();
			$tabSynonyme = $terme->getSynonymes();

			$retourAssoc = array();

			foreach ($tabAssoc as $key => $value) {
				$terme2 = $crudTerme->getByNom($value);
				if (!$terme2->isTermeVedette()) {
					$retourAssoc[] = $terme2;
				}
				
			}
			$retourTrad = array();
			foreach ($tabTrad as $key => $value) {
				$terme2 = $crudTerme->getByNom($value);
				if (!$terme2->isTermeVedette()) {
					$retourTrad[] = $terme2;
				}
			}
			$retourSyn = array();
			foreach ($tabSynonyme as $key => $value) {
				$terme2 = $crudTerme->getByNom($value);
				
				$retourSyn[] = $terme2;
				
			}
				
			asort($retourAssoc);
			asort($retourTrad);
			asort($retourSyn);


    		
    		return $this->render('ProjetBDDGeneralBundle:Default:afficheTerme.html.twig', array('terme' => $terme,
    																						'TV' => $TV,
    																						'concept' => $concept,
    																						'tabAssoc' => $retourAssoc,
    																						'tabTraduit' => $retourTrad,
    																						'tabSynonyme'=> $retourSyn));
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
    		$connect = oci_connect('SYSTEM', 'Don699mute156', 'localhost/xe');

			if (!$connect)
			{
				$e = oci_error();
				throw new \Exception('Erreur de connexion : '. $e['message']);
			}

			$requete = oci_parse($connect, 'SELECT nomTerme FROM TermeVedette t WHERE DEREF(concept).nomConcept = :nomC ');
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
			$ligne = oci_fetch_array($requete, OCI_ASSOC);

			$tabGeneralise = $concept->getGeneralise();
			$tabSpecialise = $concept->getSpecialise();

			$retourGene = array();
			foreach ($tabGeneralise as $key => $value) {
				# code...
				$concept2 = $crudConcept->getByNom($value);
				$retourGene[] = $concept2;
			}
			$retourSpe = array();
			foreach ($tabSpecialise as $key => $value) {
				# code...
				$concept2 = $crudConcept->getByNom($value);
				$retourSpe[] = $concept2;
			}

			$crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');
			$TV = $crudTermeVedette->getByNom($ligne['NOMTERME']);
			$tabAssoc = $TV->getAssocie();
			$tabTrad = $TV->getTraduit();
			$tabSynonyme = $TV->getSynonymes();

			$crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
			$retourAssoc = array();
			foreach ($tabAssoc as $key => $value) {
				$retourAssoc[] = $crudTerme->getByNom($value);
			}
			$retourTrad = array();
			foreach ($tabTrad as $key => $value) {
				$retourTrad[] = $crudTerme->getByNom($value);
			}
			
			//Synonyme ! 
			$retourSyn = array();
			$requete = oci_parse($connect, 'SELECT nomTerme FROM Terme T, Table(t.synonymes) t2 WHERE DEREF(VALUE(T2)).nomTerme = :nomT ');
			oci_bind_by_name($requete, ':nomT', $nom);
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
				$retourSyn[] = $crudTerme->getByNom($ligne['NOMTERME']);
			}

			asort($retourAssoc);
			asort($retourTrad);
			asort($retourSyn);
			asort($retourGene);
			asort($retourSpe);

			
			oci_free_statement($requete);
			oci_close($connect);
			//Il manque le tab synonymes.
			return $this->render('ProjetBDDGeneralBundle:Default:afficheConcept.html.twig', array(
				'concept' => $concept, 'TV' => $TV,'tabAssocie' => $retourAssoc,
				'tabTraduit' => $retourTrad,'tabSynonyme' => $retourSyn,
				'tabGen' => $retourGene, 'tabSpe' => $retourSpe));


    	} else {
    		return $this->render('ProjetBDDGeneralBundle:Default:afficheConcept.html.twig', array('error' => 'Aucun concept correspond à votre recherche'));
    	}
    }	
}
