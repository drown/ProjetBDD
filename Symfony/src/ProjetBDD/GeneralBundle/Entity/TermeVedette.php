<?php
/**
 * @file TermeVedette.php
 * @brief Creation d'objets TermeVedette, extend Terme
 * @author Brice V.
 * @class TermeVedette
 */
namespace ProjetBDD\GeneralBundle\Entity;

use ProjetBDD\GeneralBundle\Entity\Terme;

class TermeVedette extends Terme
{
	private $concept;

	public function setConcept($concept)
	{
		$this->concept = $concept;
	}

	public function getConcept()
	{
		return $this->concept;
	}

	public function isTermeVedette() {
		return true;
	}

	public function isConcept() {
		return false;
	}
}