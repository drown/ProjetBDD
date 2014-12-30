<?php

namespace ProjetBDD\Generalbundle\CRUD;

use ProjetBDD\Generalbundle\CRUD\CrudTerme;

class CrudTermeVedette extends CrudTerme
{
	public function getByConcept($terme)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$requete = oci_parse($connect, 'SELECT nomTerme, description FROM TermeVedette WHERE DEREF(VALUE(concept)).nomConcept = :nomC');
		oci_bind_by_name($requete, ':nomC', $terme->getNomConcept());

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
			return null;
		}
		else
		{
			while (($ligne = oci_fetch_array($requete, OCI_ASSOC)))
			{
				$terme = new Terme;
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
			}


			oci_free_statement($requete);
			oci_free_statement($requeteTraduit);
			oci_free_statement($requeteSynonymes);
			oci_free_statement($requeteAssocie);
			oci_close($connect);

			return $terme;
		}

	}

	public function update($terme)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($terme->getNomTerme());

		if ($objetTest != null)
		{

			$ligne = oci_fetch_array($requete, OCI_ASSOC);
			$requete = 'UPDATE Terme SET description = :descT WHERE nomTerme = :nom';
			oci_bind_by_name($requete, ':nom', $terme->getNomTerme());
			oci_bind_by_name($requete, ':descT', $terme->getDescription());
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


			$requete = oci_parse($connect, 'UPDATE Terme c SET c.associe = TabTerme_t(), c.traduit = TabTerme_t(), c.synonymes = TabTermeVedette_t() WHERE nomTerme = :nom');
			oci_bind_by_name($requete, ":nom", $terme->getNomTerme());
			$exe = oci_execute($requete);
			if (!$exe)
			{
				$e = oci_error();
				throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
			}

			foreach ($terme->getAssocie() as $value)
			{
				$requeteTest = oci_parse($connect, 'SELECT nomTerme FROM Terme WHERE nomTerme = :nom');
				oci_bind_by_name($requeteTest, 'nom', $value->getNomTerme());
				$exe = oci_execute($requeteTest);

				if (oci_num_rows($requeteTest) > 0)
					$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.associe FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM Terme c2 WHERE c2.nomTerme = :nom2))');
				else
					$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.associe FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');

				oci_bind_by_name($requete, ':nom', $terme->getNomTerme());
				oci_bind_by_name($requete, ':nom2', $value->getNomTerme());

				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
					
			}

			foreach ($terme->getTraduit() as $value)
			{
				$requeteTest = oci_parse($connect, 'SELECT nomTerme FROM Terme WHERE nomTerme = :nom');
				oci_bind_by_name($requeteTest, 'nom', $value->getNomTerme());
				$exe = oci_execute($requeteTest);

				if (oci_num_rows($requeteTest) > 0)
					$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM Terme c2 WHERE c2.nomTerme = :nom2))');
				else
					$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');

				oci_bind_by_name($requete, ':nom', $terme->getNomTerme());
				oci_bind_by_name($requete, ':nom2', $value->getNomTerme());

				$exe = oci_execute($requete);
				if (!$exe)
				{
					$e = oci_error();
					throw new \Exception('Erreur d\' éxécution de la requête : '. $e['message']);
				}
				
			}

			foreach ($terme->getSynonymes() as $value)
			{
				$requete = oci_parse($connect, 'INSERT INTO Table(SELECT c.traduit FROM TermeVedette c WHERE nomTerme = :nom) Values ((SELECT REF(c2) FROM TermeVedette c2 WHERE c2.nomTerme = :nom2))');

				oci_bind_by_name($requete, ':nom', $terme->getNomTerme());
				oci_bind_by_name($requete, ':nom2', $value->getNomTerme());

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

			throw new \Exception('Erreur : le TermeVedette n\'existe pas !')
		}

		oci_free_statement($requete);
		oci_close($connect);
	}

	public function creer($terme)
	{
		$connect = oci_connect('ProjetBDD', 'pass', 'localhost/xe');

		if (!$connect)
		{
			$e = oci_error();
			throw new \Exception('Erreur de connexion : '. $e['message']);
		}

		$objetTest = $this->getByNom($terme->getNomTerme());

		if $objetTest == null)
		{
			$requete = oci_parse($connect, 'INSERT INTO TermeVedette VALUES (:nom, :descT, tabTerme_t(), tabTerme_t(), TabTermeVedette_t())');
			oci_bind_by_name($connect, ':nom', $terme->getNomTerme());
			oci_bind_by_name($connect, ':descT', $terme->getDescription());
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

	public function remove($terme)
	{

	}
}