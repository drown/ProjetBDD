<?php

namespace ProjetBDD\Generalbundle\CRUD;

use ProjetBDD\Generalbundle\CRUD\CrudTerme;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;

class CrudTermeVedette extends CrudTerme
{
	public function getByConcept($concept)
	{
		$nomConcept = $concept->getNomConcept();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomTerme, description FROM TermeVedette WHERE DEREF(concept).nomConcept = :nomC');
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

		$terme = null;

			while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
			{
				$terme = new TermeVedette;
				$terme->setNomTerme($ligne['NOMTERME']);
				$terme->setDescription($ligne['DESCRIPTION']);

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

			oci_free_statement($requete);
			oci_free_statement($requeteTraduit);
			oci_free_statement($requeteSynonymes);
			oci_free_statement($requeteAssocie);
			oci_close($connect);

			return $terme;
		}

	}

	public function creer($terme)
	{
		$nomTerme = $terme->getNomTerme();
		$descriptionTerme = $terme->getDescription();
		$concept = $terme->getConcept()->getNomConcept();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($terme->getNomTerme());

		if ($objetTest == null)
		{
			$requete = oci_parse($connect, 'INSERT INTO TermeVedette VALUES (:nom, :descT, tabTerme_t(), tabTerme_t(), TabTermeVedette_t(), (SELECT REF(c) FROM Concept c WHERE c.nomConcept = :nomC))');
			oci_bind_by_name($requete, ':nom', $nomTerme);
			oci_bind_by_name($requete, ':descT', $descriptionTerme);
			oci_bind_by_name($requete, ':nomC', $concept);
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

	public function supprimer($terme)
	{
		$nomTerme = $terme->getNomTerme();

		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($terme->getNomTerme());
		if ($objetTest != null)
		{

			oci_free_statement($requete);
			oci_close($connect);
			throw new \Exception('Erreur : Terme non existant !');
			
		}
		else
		{
			foreach ($terme->getAssocie() as $t)
			{
				$t2 = $this->getByNom($t->getNomTerme());
				$t2->removeAssocie($t);
				if (get_class($c2) == 'Terme')
					$this->update($t2);
				else
					$this->updateVedette($t2);
			}

			foreach ($terme->getTraduit() as $t)
			{
				$t2 = $this->getByNom($t->getNomTerme());
				$t2->removeTraduit($t);
				if (get_class($c2) == 'Terme')
					$this->update($t2);
				else
					$this->updateVedette($t2);
			}

			foreach ($terme->getSynonymes() as $t)
			{
				$t2 = $this->getByNom($t->getNomTerme());
				$t2->removeSynonymes($t);
				$this->updateVedette($t2);

			}

			$requeteDelete = oci_parse($connect, 'DELETE FROM TermeVedette WHERE nomTerme = :nomC');
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
			oci_free_statement($requeteDelete);
			oci_close($connect);
		}
	}
}