<?php

namespace ProjetBDD\GeneralBundle\Entity

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
}