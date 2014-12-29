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

		return $tabConcept;
	}

	public function getByNom($nom)
	{

	}

	public function getByTermeVedette($terme)
	{

	}

	public function update($concept)
	{

	}

	public function creer($concept)
	{

	}

	public function supprimer($concept)
	{

	}
}