<?php

/**
 * @file CrudTerme.php
 * @brief CRUD pour les Termes
 * @author Brice V.
 * @class CrudTermeVedette
 */

namespace ProjetBDD\Generalbundle\CRUD;

use ProjetBDD\GeneralBundle\Entity\Terme;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;
use ProjetBDD\GeneralBundle\CRUD\CrudConcept;



class CrudTerme
{
	/*
	@author Brice V.
	@action Creer un terme
	@param Objet terme
	@return rien
	*/
	public function creer($terme)
	{
		$nomTerme = $terme->getNomTerme();
		$descriptionTerme = $terme->getDescription();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($terme->getNomTerme());

		if ($objetTest == null)
		{
			$requete = oci_parse($connect, 'INSERT INTO Terme VALUES (:nom, :descT, tabTerme_t(), tabTerme_t(), TabTermeVedette_t())');
			oci_bind_by_name($requete, ':nom', $nomTerme);
			oci_bind_by_name($requete, ':descT', $descriptionTerme);

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

			oci_free_statement($requete);
			oci_close($connect);
			
		}
		else
		{
			oci_free_statement($requete);
			oci_close($connect);

			throw new \Exception('Terme ou TermeVedette déjà existant');
			
		}


	}

	/*
	@author Brice V.
	@action recupere tout les termes
	@param 
	@return tableau de tout les termes
	*/
	public function getAll()
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');


		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomTerme, description FROM Terme ORDER BY nomTerme ASC');

		$exe = oci_execute($requete);

		$tabTerme = array();

		while ($ligne = oci_fetch_array($requete, OCI_ASSOC))
		{
			$tabTerme[] = new Terme;
			end($tabTerme)->setNomTerme($ligne['NOMTERME']);
			end($tabTerme)->setDescription($ligne['DESCRIPTION']);
		}

		return $tabTerme;

	}

	/*
	@author Brice V.
	@action recupere plusieurs termes via un mot clef
	@param mot clef
	@return tableau de termes qui possede ce mot clef en tant que nom
	*/
	public function findByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomTerme, description FROM Terme WHERE LOWER(nomTerme) LIKE LOWER(:debut) OR LOWER(nomTerme) LIKE LOWER(:milieu) OR LOWER(nomTerme) LIKE LOWER(:fin) OR LOWER(nomTerme) = LOWER(:nom) ORDER BY nomTerme ASC');
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

		$tabTerme = array();

		while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
		{
			$tabTerme[] = new Terme;
			end($tabTerme)->setNomTerme($ligne['NOMTERME']);
			end($tabTerme)->setDescription($ligne['DESCRIPTION']);

			$requeteAssocie = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomTerme as nom FROM Terme c, TABLE(c.associe) g WHERE c.nomTerme = :nomC');
			oci_bind_by_name($requeteAssocie, ':nomC', $ligne['NOMTERME']);

			$exe = oci_execute($requeteAssocie);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneAssocie = oci_fetch_array($requeteAssocie, OCI_ASSOC)))
				end($tabTerme)->addAssocie($ligneAssocie['NOM']);

			$requeteTraduit = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomTerme as nom FROM Terme c, Table(c.traduit) s WHERE c.nomTerme = :nomC');
			oci_bind_by_name($requeteTraduit, ':nomC', $ligne['NOMTERME']);

			$exe = oci_execute($requeteTraduit);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneTraduit = oci_fetch_array($requeteTraduit, OCI_ASSOC)))
				end($tabTerme)->addTraduit($ligneTraduit['NOM']);


			$requeteSynonymes = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomTerme as nom FROM Terme c, Table(c.synonymes) s WHERE c.nomTerme = :nomC');
			oci_bind_by_name($requeteSynonymes, ':nomC', $ligne['NOMTERME']);

			$exe = oci_execute($requeteSynonymes);

			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}
		
			while (($ligneSynonymes = oci_fetch_array($requeteSynonymes, OCI_ASSOC)))
				end($tabTerme)->addSynonymes($ligneSynonymes['NOM']);		

			oci_free_statement($requeteAssocie);
			oci_free_statement($requeteTraduit);
			oci_free_statement($requeteSynonymes);
		}

		oci_free_statement($requete);
		
		oci_close($connect);

		if (count($tabTerme) == 0)
			return null;
		else
			return $tabTerme;
	}

	/*
	@author Brice V.
	@action Recupere un terme precis
	@param nom du terme
	@return objet terme
	*/
	public function getByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT COUNT(*) AS cpt FROM Terme WHERE nomTerme = :nomC');
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

		$terme = null;

		$ligne = oci_fetch_array($requete, OCI_ASSOC);

		if ($ligne['CPT'] == 0)
			return $this->getVedetteByNom($nom);

		oci_free_statement($requete);

		$requete = oci_parse($connect, 'SELECT nomTerme, description FROM Terme WHERE nomTerme = :nomC');
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

				$terme = new Terme;
				$terme->setNomTerme($ligne['NOMTERME']);
				$terme->setDescription($ligne['DESCRIPTION']);

				$requeteAssocie = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomTerme as nom FROM Terme c, TABLE(c.associe) g WHERE c.nomTerme = :nomC');
				oci_bind_by_name($requeteAssocie, ':nomC', $ligne['NOMTERME']);

				$exe = oci_execute($requeteAssocie);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
		
				while (($ligneAssocie = oci_fetch_array($requeteAssocie, OCI_ASSOC)))
					$terme->addAssocie($ligneAssocie['NOM']);

				$requeteTraduit = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomTerme as nom FROM Terme c, Table(c.traduit) s WHERE c.nomTerme = :nomC');
				oci_bind_by_name($requeteTraduit, ':nomC', $ligne['NOMTERME']);

				$exe = oci_execute($requeteTraduit);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
		
				while (($ligneTraduit = oci_fetch_array($requeteTraduit, OCI_ASSOC)))
					$terme->addTraduit($ligneTraduit['NOM']);


				$requeteSynonymes = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomTerme as nom FROM Terme c, Table(c.synonymes) s WHERE c.nomTerme = :nomC');
				oci_bind_by_name($requeteSynonymes, ':nomC', $ligne['NOMTERME']);

				$exe = oci_execute($requeteSynonymes);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
		
				while (($ligneSynonymes = oci_fetch_array($requeteSynonymes, OCI_ASSOC)))
					$terme->addSynonymes($ligneSynonymes['NOM']);		

			oci_free_statement($requete);
			oci_close($connect);
 
			return $terme;
		
	}

	/*
	@author Brice V.
	@action recupere un terme vedette
	@param nom du terme vedette
	@return objet termevedette
	*/
	public function getVedetteByNom($nom)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomTerme, description, DEREF(concept).nomConcept AS nomConcept FROM TermeVedette WHERE nomTerme = :nomC');
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
		$terme = null;

			while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
			{
				$terme = new TermeVedette;
				$terme->setNomTerme($ligne['NOMTERME']);
				$terme->setDescription($ligne['DESCRIPTION']);
				$terme->setConcept($ligne['NOMCONCEPT']);

				$requeteAssocie = oci_parse($connect, 'SELECT DEREF(VALUE(g)).nomTerme as nom FROM TermeVedette c, TABLE(c.associe) g WHERE c.nomTerme = :nomC');
				oci_bind_by_name($requeteAssocie, ':nomC', $ligne['NOMTERME']);

				$exe = oci_execute($requeteAssocie);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
		
				while (($ligneAssocie = oci_fetch_array($requeteAssocie, OCI_ASSOC)))
					$terme->addAssocie($ligneAssocie['NOM']);

				$requeteTraduit = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomTerme as nom FROM TermeVedette c, Table(c.traduit) s WHERE c.nomTerme = :nomC');
				oci_bind_by_name($requeteTraduit, ':nomC', $ligne['NOMTERME']);

				$exe = oci_execute($requeteTraduit);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
		
				while (($ligneTraduit = oci_fetch_array($requeteTraduit, OCI_ASSOC)))
					$terme->addTraduit($ligneTraduit['NOM']);


				$requeteSynonymes = oci_parse($connect, 'SELECT DEREF(VALUE(s)).nomTerme as nom FROM TermeVedette c, Table(c.synonymes) s WHERE c.nomTerme = :nomC');
				oci_bind_by_name($requeteSynonymes, ':nomC', $ligne['NOMTERME']);

				$exe = oci_execute($requeteSynonymes);

				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
		
				while (($ligneSynonymes = oci_fetch_array($requeteSynonymes, OCI_ASSOC)))
					$terme->addSynonymes($ligneSynonymes['NOM']);		
			}


			oci_free_statement($requete);
			oci_close($connect);

			return $terme;
	}

	/*
	@author Brice V.
	@action Met a jour un terme grace a l'objet
	@param Objet terme
	@return rien
	*/
	public function update($terme)
	{
		$nomTerme = $terme->getNomTerme();
		$descriptionTerme = $terme->getDescription();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($terme->getNomTerme());

		if ($objetTest != null)
		{
			if ($terme instanceof TermeVedette)
				$requete = oci_parse($connect, 'UPDATE TermeVedette SET description = :descT WHERE nomTerme = :nom');
			else
				$requete = oci_parse($connect, 'UPDATE Terme SET description = :descT WHERE nomTerme = :nom');

			oci_bind_by_name($requete, ':nom', $nomTerme);
			oci_bind_by_name($requete, ':descT', $descriptionTerme);
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

			if ($terme instanceof TermeVedette)

				$requete = oci_parse($connect, 'UPDATE TermeVedette c SET c.associe = TabTerme_t(), c.traduit = TabTerme_t(), c.synonymes = TabTermeVedette_t() WHERE nomTerme = :nom');
			else
				$requete = oci_parse($connect, 'UPDATE Terme c SET c.associe = TabTerme_t(), c.traduit = TabTerme_t(), c.synonymes = TabTermeVedette_t() WHERE nomTerme = :nom');

			oci_bind_by_name($requete, ":nom", $nomTerme);
			$exe = oci_execute($requete);
			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}

			foreach ($terme->getAssocie() as $value)
			{
				$requeteTest = oci_parse($connect, 'SELECT COUNT(*) AS cpt FROM Terme WHERE nomTerme = :nom');
				oci_bind_by_name($requeteTest, 'nom', $value);
				$exe = oci_execute($requeteTest);
				$ligneTest = oci_fetch_array($requeteTest, OCI_ASSOC);

				if ($terme instanceof TermeVedette)
				{
					if ($ligneTest['CPT'] == 1)
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.associe FROM TermeVedette c WHERE nomTerme = :nom) Values((SELECT REF(c2) FROM Terme c2 WHERE c2.nomTerme = :nom2))');
					else
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.associe FROM TermeVedette c WHERE nomTerme = :nom) Values((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');
				}
				else
				{
					if ($ligneTest['CPT'] == 1)
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.associe FROM Terme c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM Terme c2 WHERE c2.nomTerme = :nom2))');
					else
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.associe FROM Terme c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');
				}


				oci_bind_by_name($requete, ':nom', $nomTerme);
				oci_bind_by_name($requete, ':nom2', $value);

				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
					
			}

			foreach ($terme->getTraduit() as $value)
			{
				$requeteTest = oci_parse($connect, 'SELECT COUNT(*) AS cpt FROM Terme WHERE nomTerme = :nom');
				oci_bind_by_name($requeteTest, 'nom', $value);
				$exe = oci_execute($requeteTest);
				$ligneTest = oci_fetch_array($requeteTest, OCI_ASSOC);

				if ($terme instanceof TermeVedette)
				{
					if ($ligneTest['CPT'] == 1)
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM Terme c2 WHERE c2.nomTerme = :nom2))');
					else
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');
				}
				else
				{
					if ($ligneTest['CPT'] == 1)
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM Terme c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM Terme c2 WHERE c2.nomTerme = :nom2))');
					else
						$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM Terme c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');
				}

				oci_bind_by_name($requete, ':nom', $nomTerme);
				oci_bind_by_name($requete, ':nom2', $value);

				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				
			}

			foreach ($terme->getSynonymes() as $value)
			{
				if ($terme instanceof TermeVedette)
					$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.synonymes FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');
				else
					$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.synonymes FROM Terme c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');

				oci_bind_by_name($requete, ':nom', $nomTerme);
				oci_bind_by_name($requete, ':nom2', $value);

				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				
			}
		}
		else
		{
			oci_free_statement($requete);
			oci_close($connect);

			throw new \Exception('Erreur : le Terme n\'existe pas !');
		}

		oci_free_statement($requete);
		oci_close($connect);
	}
	

	/*
	@author Brice V.
	@action Supprime un terme
	@param Objet terme
	@return rien
	*/
	public function supprimer($terme)
	{
		$nomTerme = $terme->getNomTerme();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomTerme, description FROM Terme WHERE nomTerme = :nom');
		oci_bind_by_name($requete, ':nom', $nomTerme);

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

			foreach ($terme->getAssocie() as $t)
			{
				$t2 = $this->getByNom($t);
				$t2->removeAssocie($terme->getNomTerme());
				$this->update($t2);
			}

			foreach ($terme->getTraduit() as $t)
			{
				$t2 = $this->getByNom($t);
				$t2->removeTraduit($terme->getNomTerme());
				$this->update($t2);

			}

			if ($terme instanceof TermeVedette)
			{
				foreach ($terme->getSynonymes() as $t)
				{
					$t2 = $this->getByNom($t);
					$t2->removeSynonymes($terme->getNomTerme());
					$this->update($t2);
				}
			}

			if ($terme instanceof TermeVedette)
				$requeteDelete = oci_parse($connect, 'DELETE FROM TermeVedette WHERE nomTerme = :nomC');
			else
				$requeteDelete = oci_parse($connect, 'DELETE FROM Terme WHERE nomTerme = :nomC');

			oci_bind_by_name($requeteDelete, ':nomC', $nomTerme);
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
			oci_free_statement($requeteDelete);;
			oci_close($connect);
	}
}