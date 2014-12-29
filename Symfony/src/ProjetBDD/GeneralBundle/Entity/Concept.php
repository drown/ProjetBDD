<?php

namespace ProjetBDD\GeneralBundle\Entity;

class Concept
{
	private $nomConcept;
	private $description;
	private $generalise;
	private $specialise;

	public function __construct()
	{
		$this->generalise = array();
		$this->specialise = array();
	}

	public function setNomConcept($nom)
	{
		$this->nomConcept = $nom;
	}

	public function getNomConcept()
	{
		return $this->nomConcept;
	}

	public function setDescription($desc)
	{
		$this->description = $desc;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function addGeneralise($concept)
	{
		$this->generalise[] = $concept;
	}

	public function getGeneralise()
	{
		return $this->generalise;
	}

	public function removeGeneralise($concept)
	{
		// remove un élément du tableau.
	}

	public function addSpecialise($concept)
	{
		$this->specialise[] = $concept;
	}

	public function getSpecialise()
	{
		return $this->specialise;
	}

	public function removeSpecialise($concept)
	{
		// remove un élément du tableau.
	}
}