<?php

/**
 * @file CrudConcept.php
 * @brief CRUD pour les Concept
 * @author Baptiste L.
 * @class CrudConcept
 */

namespace ProjetBDD\Generalbundle\CRUD;

use ProjetBDD\GeneralBundle\Entity\Concept;


class CrudConcept
{
	/*
	@author Baptiste L.
	@action recupere tout les concepts de la BD 
	@param 
	@return tableau de tout les concepts
	*/
	public function getAll()
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');


		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept ORDER BY nomConcept ASC');

		$exe = oci_execute($requete);

		$tabConcept = array();

		while ($ligne = oci_fetch_array($requete, OCI_ASSOC))
		{
			$tabConcept[] = new Concept;
			end($tabConcept)->setNomConcept($ligne['NOMCONCEPT']);
			end($tabConcept)->setDescription($ligne['DESCRIPTION']);
		}

		return $tabConcept;

	}

	/*
	@author Baptiste L.
	@action recupere tout les concept en fonction d'un mot cle 
	@param nom d'un concept
	@return tableau de tout les concepts disponible avec une partie du param
	*/
	public function findByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE LOWER(nomConcept) LIKE LOWER(:debut) OR LOWER(nomConcept) LIKE LOWER(:milieu) OR LOWER(nomConcept) LIKE LOWER(:fin) OR LOWER(nomConcept) = LOWER(:nom) ORDER BY nomConcept ASC');
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
		oci_close($connect);

		if (count($tabConcept) == 0)
			return null;
		else
			return $tabConcept;
	}

	/*
	@author Baptiste L.
	@action recupere un concept specifique
	@param nom d'un concept
	@return son objet php
	*/
	public function getByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE nomConcept = :nomC');
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
		/*if (oci_num_rows($requete) == 0) {

			oci_free_statement($requete);
			oci_close($connect);
			return null;
		}*/
		/*else
		{*/
			$concept = null;

			while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
			{
				$concept = new Concept;
				$concept->setNomConcept($ligne['NOMCONCEPT']);
				$concept->setDescription($ligne['DESCRIPTION']);
				$requeteGeneralise = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomConcept as nom FROM Concept c, TABLE(c.generalise) g WHERE c.nomConcept = :nomC');
				oci_bind_by_name($requeteGeneralise, ':nomC', $ligne['NOMCONCEPT']);

				$exe = oci_execute($requeteGeneralise);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
			
				while (($ligneGeneralise = oci_fetch_array($requeteGeneralise, OCI_ASSOC))) {
					$concept->addGeneralise($ligneGeneralise['NOM']);
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
					$concept->addSpecialise($ligneSpecialise['NOM']);
				}	
			}


			oci_free_statement($requete);
			oci_close($connect);

			return $concept;		
	}

	/*
	@author Baptiste L.
	@action Met a jour la BD avec un objet concept
	@param Objet Concept
	@return rien
	*/
	public function update($concept)
	{
		$nomConcept = $concept->getNomConcept();
		$descriptionConcept = $concept->getDescription();
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}
		//On recupere le nom et description du concept
		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE nomConcept = :nomC');
		oci_bind_by_name($requete, ':nomC', $nomConcept);
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
		//if (oci_num_rows($requete) == 1) {
			//On met a jour le nom et la description
			$ligne = oci_fetch_array($requete, OCI_ASSOC);
			$requete = oci_parse($connect, 'UPDATE Concept SET nomConcept = :nomC, description = :descC WHERE nomConcept = :nomC');
			oci_bind_by_name($requete, ':nomC', $nomConcept);
			oci_bind_by_name($requete, ':descC', $descriptionConcept);
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

			//On vite les listes specialise et generalise
			//On les re remplis avec le tableau de concept
			$requete = oci_parse($connect, 'UPDATE Concept c SET c.generalise = TabConcept_t(), c.specialise = TabConcept_t() WHERE nomConcept = :nomC');
			oci_bind_by_name($requete, ":nomC", $nomConcept);
			$exe = oci_execute($requete);
			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}

			foreach ($concept->getSpecialise() as $key => $value) {

				//On ajoute
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.specialise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values (
													(SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2)
														)');
				oci_bind_by_name($requete, ':nomC', $nomConcept);
				oci_bind_by_name($requete, ':nomC2', $value);
				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
					
			}

			foreach ($concept->getGeneralise() as $key => $value) {
				
				//On ajoute
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.generalise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values ((
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2)
														)');
				oci_bind_by_name($requete, ':nomC', $nomConcept);
				oci_bind_by_name($requete, ':nomC2', $value);
				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				
			}
		//}
	}

	/*
	@author Baptiste L.
	@action creer un concept
	@param Objet concept
	@return rien
	*/
	public function creer($concept)
	{
		$nomConcept = $concept->getNomConcept();
		$descriptionConcept = $concept->getDescription();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($nomConcept);
		
		if ($objetTest == null)
		{
			//Le concept existe pas, on insere
			$requete = oci_parse($connect, 'INSERT INTO Concept VALUES (:nomC, :descC, tabConcept_t(), tabConcept_t())');
			oci_bind_by_name($requete, ':nomC', $nomConcept);
			oci_bind_by_name($requete, ':descC', $descriptionConcept);

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
			foreach ($concept->getGeneralise() as $key => $value) {
				//On insere dans le tableau generalise la reference d'un concept c2
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.generalise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values ((
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2)
														)');
				oci_bind_by_name($requete, ':nomC', $nomConcept);
				oci_bind_by_name($requete, ':nomC2', $value);
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

			foreach ( $concept->getSpecialise() as $key => $value) {
				$requete = oci_parse($connect, 'INSERT INTO Table(
													SELECT c.specialise 
													FROM Concept c 
													WHERE nomConcept = :nomC) 
												Values ((
													SELECT REF(c2) 
													FROM Concept c2 
													WHERE c2.nomConcept = :nomC2)
														)');
				oci_bind_by_name($requete, ':nomC', $nomConcept);
				oci_bind_by_name($requete, ':nomC2', $value);
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
	/*
	@author Baptiste L.
	@action supprime un concept
	@param objet Concept
	@return rien
	*/
	public function supprimer($concept)
	{
		$nomConcept = $concept->getNomConcept();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomConcept, description FROM Concept WHERE nomConcept = :nomC');
		oci_bind_by_name($requete, ':nomC', $nomConcept);

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

			//mise a jour des ref dans tout les autres concept... RELOU!
			
			foreach ($concept->getSpecialise() as $c)
			{
				$c2 = $this->getByNom($c);
				$c2->removeGeneralise($concept->getNomConcept());
				$this->update($c2);
			}

			foreach ($concept->getGeneralise() as $c)
			{
				$c2 = $this->getByNom($c);
				$c2->removeSpecialise($concept->getNomConcept());
				$this->update($c2);
			}

			

			//Suppresion
			$requeteDelete = oci_parse($connect, 'DELETE FROM Concept WHERE nomConcept = :nomC');
			oci_bind_by_name($requeteDelete, ':nomC', $nomConcept);
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
			oci_close($connect);
		}
}