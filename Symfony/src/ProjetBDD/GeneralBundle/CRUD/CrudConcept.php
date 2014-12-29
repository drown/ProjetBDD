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
					$concept->addGeneralise($ligneGeneralise)
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
					$concept->addSpecialise($ligneSpecialise)
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

	}

	public function creer($concept)
	{

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
			$requeteUpdate = "SELECT REF(c) FROM Concept c WHERE nomConcept = :nomC";
			ocibindbyname($requeteUpdate, ':nomC', $concept->nomConcept);
			//mise a jour des ref dans tout les autres concept... RELOU!
			$requete = oci_parse($connect, 'SELECT nomConcept FROM Concept');
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

			}
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