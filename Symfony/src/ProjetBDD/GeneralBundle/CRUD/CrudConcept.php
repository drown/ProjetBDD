<?php

namespace ProjetBDD\Generalbundle\CRUD;

use ProjetBDD\GeneralBundle\Entity\Concept;


class CrudConcept
{
	public function findByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE LOWER(nomConcept) LIKE LOWER(:debut) OR LOWER(nomConcept) LIKE LOWER(:milieu) OR LOWER(nomConcept) LIKE LOWER(:fin) OR LOWER(nomConcept) = LOWER(:nom)');
		$milieu = '%' . $nom . '%';
		$debut = $nom . '%';
		$fin = '%' . $nom;
		oci_bind_by_name($requete, ':debut', $debut);
		oci_bind_by_name($requete, ':milieu', $milieu);
		oci_bind_by_name($requete, ':fin', $fin);
		oci_bind_by_name($requete, ':nom', $nom);


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

		$tabConcept = array();

		while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
		{
			$tabConcept[]= new Concept;
			end($tabConcept)->setNomConcept($ligne['NOMCONCEPT']);
			end($tabConcept)->setDescription($ligne['DESCRIPTION']);

			$requeteGeneralise = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomConcept as nom FROM Concept c, TABLE(c.generalise) g WHERE c.nomConcept = :nomC');
			oci_bind_by_name($requeteGeneralise, ':nomC', $ligne['NOMCONCEPT']);

			$exe = oci_execute($requeteGeneralise);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneGeneralise = oci_fetch_array($requeteGeneralise, OCI_ASSOC)))
				end($tabConcept)->addGeneralise($ligneGeneralise['NOM']);

			$requeteSpecialise = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomConcept as nom FROM Concept c, Table(c.specialise) s WHERE c.nomConcept = :nomC');
			oci_bind_by_name($requeteSpecialise, ':nomC', $ligne['NOMCONCEPT']);

			$exe = oci_execute($requeteSpecialise);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneSpecialise = oci_fetch_array($requeteSpecialise, OCI_ASSOC)))
				end($tabConcept)->addSpecialise($ligneSpecialise['NOM']);			
		}

		oci_free_statement($requete);
		oci_free_statement($requeteSpecialise);
		oci_free_statement($requeteGeneralise);
		oci_close($connect);

		return $tabConcept;
	}

	public function getByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE nomConcept = :nomC');
		ocibindbyname($requete, ':nomC', $nom);

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
		if (oci_num_rows($requete) == 0) {

			oci_free_statement($requete);
			oci_close($connect);
			return NULL;
		}
		else {
			while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
			{
				$concept = new Concept;
				$concept->setNomConcept($ligne['nomConcept']);
				$concept->setDescription($ligne['description']);
				$requeteGeneralise = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomConcept as nom FROM Concept c, TABLE(c.generalise) g WHERE c.nomConcept = :nomC');
				oci_bind_by_name($requeteGeneralise, ':nomC', $ligne['NOMCONCEPT']);

				$exe = oci_execute($requeteGeneralise);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
			
				while (($ligneGeneralise = oci_fetch_array($requeteGeneralise, OCI_ASSOC))) {
					$concept->addGeneralise($ligneGeneralise);
				}
				

				$requeteSpecialise = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomConcept as nom FROM Concept c, Table(c.specialise) s WHERE c.nomConcept = :nomC');
				oci_bind_by_name($requeteSpecialise, ':nomC', $ligne['NOMCONCEPT']);

				$exe = oci_execute($requeteSpecialise);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
			
				while (($ligneSpecialise = oci_fetch_array($requeteSpecialise, OCI_ASSOC))) {
					$concept->addSpecialise($ligneSpecialise);
				}	
			}


			oci_free_statement($requete);
			oci_free_statement($requeteSpecialise);
			oci_free_statement($requeteGeneralise);
			oci_close($connect);

			return $concept;
		}
		
	}

	public function update($concept)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}
		//On recupere le nom et description du concept
		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE nomConcept = :nomC');
		ocibindbyname($requete, ':nomC', $concept->nomConcept);
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
		if (oci_num_rows($requete) == 1) {
			//On met a jour le nom et la description
			$ligne = oci_fetch_array($requete, OCI_ASSOC);
			$requete = 'UPDATE Concept SET nomConcept = :nomC, description = :desc WHERE nomConcept = :nomC';
			ocibindbyname($requete, ':nomC', $concept->getNomConcept());
			ocibindbyname($requete, ':descC', $concept->getDescription());
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

			// On recupere dans un tableau php, tout les noms de concept associe a ce concept
			$tabSpecialise = array();
			$tabGeneralise = array();
			$requete = oci_parse($connect, 'SELECT DEREF(VALUE(T)).nomConcept as nom FROM Concept c, Table(c.specialise) T WHERE nomConcept = :nomC');
			ocibindbyname($requete, ':nomC', $concept->getNomConcept());
			$exe = oci_execute($requete);
			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneSpecialise = oci_fetch_array($requete, OCI_ASSOC))) {
				$tabSpecialise[] = $ligneSpecialise['nom'];
			}

			$requete = oci_parse($connect, 'SELECT DEREF(VALUE(T)).nomConcept as nom FROM Concept c, Table(c.generalise) T WHERE nomConcept = :nomC');
			ocibindbyname($requete, ':nomC', $concept->getNomConcept());
			$exe = oci_execute($requete);
			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneGeneralise = oci_fetch_array($requete, OCI_ASSOC))) {
				$tabGeneralise[] = $ligneGeneralise['nom'];
			}

			//On vite les listes specialise et generalise
			//On les re remplis avec le tableau de concept
			$requete = oci_parse($connect, 'UPDATE Concept c SET c.generalise = TabConcept_t(), c.specialise = TabConcept_t() WHERE nomConcept = :nomC');
			ocibindbyname($requete, ":nomC", $concept->getNomConcept());
			$exe = oci_execute($requete);
			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}

			foreach ($concept->specialise as $key => $value) {

				//On ajoute
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.specialise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values (
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2
														)');
				ocibindbyname($requete, ':nomC', $concept->getNomConcept());
				ocibindbyname($requete, ':nomC2', $value->getNomConcept());
				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
					
			}

			foreach ($concept->generalise as $key => $value) {
				
				//On ajoute
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.generalise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values (
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2
														)');
				ocibindbyname($requete, ':nomC', $concept->getNomConcept());
				ocibindbyname($requete, ':nomC2', $value->getNomConcept());
				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				
			}

			
	}

	public function creer($concept)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept FROM Concept WHERE nomConcept = :nomC');
		ocibindbyname($requete, ':nomC', $concept->nomConcept);
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
		if (oci_num_rows($requete) == 0) {
			//Le concept existe pas, on insere
			$requete = oci_parse($connect, 'INSERT INTO Concept VALUES (:nomC, :descC, tabConcept_t(), tabConcept_t());');
			ocibindbyname($connect, ':nomC', $concept->getNomConcept());
			ocibindbyname($connect, ':descC', $concept->getDescription());
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
			foreach ($concept->generalise as $key => $value) {
				//On insere dans le tableau generalise la reference d'un concept c2
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.generalise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values (
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2
														)');
				ocibindbyname($requete, ':nomC', $concept->getNomConcept());
				ocibindbyname($requete, ':nomC2', $value);
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
			}

			foreach ($specialise as $key => $value) {
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.specialise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values (
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2
														)');
				ocibindbyname($requete, ':nomC', $concept->getNomConcept());
				ocibindbyname($requete, ':nomC2', $value);
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
			}


			oci_free_statement($requete);
			oci_close($connect);
			
		}
	}

	public function supprimer($concept)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE nomConcept = :nomC');
		ocibindbyname($requete, ':nomC', $concept->nomConcept);

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
		if (oci_num_rows($requete) == 0) {

			oci_free_statement($requete);
			oci_close($connect);
			return NULL;
		}
		else if (oci_num_rows($requete) == 1) {

			//mise a jour des ref dans tout les autres concept... RELOU!
			/*$requete = oci_parse($connect, 'SELECT nomConcept FROM Concept');
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
				//On parcours la liste des concepts generalise et specialise pour chaque concept..
				$requeteGeneralise = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomConcept as nom FROM Concept c, TABLE(c.generalise) g WHERE c.nomConcept = :nomC');
				ocibindbyname($requeteGeneralise, ':nomC', $ligne['nomConcept']);
				$exe = oci_execute($requeteGeneralise);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
			
				while (($ligneGeneralise = oci_fetch_array($requeteGeneralise, OCI_ASSOC))) {
					if ($ligneGeneralise['nomConcept'] == $concept->nomConcept) {
						//Ici, il faut update..
					}
				}

				$requeteSpecialise = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomConcept as nom FROM Concept c, TABLE(c.specialise) g WHERE c.nomConcept = :nomC');
				ocibindbyname($requeteSpecialise, ':nomC', $ligne['nomConcept']);
				$exe = oci_execute($requeteSpecialise);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
			
				while (($ligneSpecialise = oci_fetch_array($requeteSpecialise, OCI_ASSOC))) {
					if ($ligneSpecialise['nomConcept'] == $concept->nomConcept) {
						//Ici, il faut update..
					}
				}

			}*/


			//Suppresion
			$requeteDelete = oci_parse($connect, 'DELETE FROM Concept WHERE nomConcept = :nomC');
			ocibindbyname($requeteDelete, ':nomC', $concept->nomConcept);
			if (!$requeteDelete)
			{
				$e = oci_error();
				throw new \Exception('Erreur de requête : '. $e['message']);
			}

			$exe = oci_execute($requeteDelete);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
			oci_free_statement($requete);
			oci_free_statement($requeteDelete);
			oci_free_statement($requeteSpecialise);
			oci_free_statement($requeteGeneralise);
			oci_close($connect);
		}
		else {
			//Erreur dans les contraintes d'intégrité
			//Code erreur?
		}
	}
}