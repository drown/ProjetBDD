<?php

namespace ProjetBDD\GeneralBundle\Entity;

class Terme
{
	protected $nomTerme;
	protected $description;
	protected $associe;
	protected $traduit;
	protected $synonymes;

	public function __construct()
	{
		$this->associe = array();
		$this->traduit = array();
		$this->synonymes = array();
	}

	public function setNomTerme($nom)
	{
		$this->nomTerme = $nom;
	}

	public function getNomTerme()
	{
		return $this->nomTerme;
	}

	public function setDescription($desc)
	{
		$this->description = $desc;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function addAssocie($terme)
	{
		$this->associe[] = $terme;
	}

	public function getAssocie()
	{
		return $this->associe;
	}

	public function removeAssocie($associe)
	{
		// remove un élément du tableau.
		foreach ($this->associe as $i => $value) {
			if ($value == $associe) {
				unset($this->associe[$i]);
				$this->associe = array_values($this->associe);
			}
		}
	}

	public function addTraduit($terme)
	{
		$this->traduit[] = $terme;
	}

	public function getTraduit()
	{
		return $this->traduit;
	}

	public function removeTraduit($terme)
	{
		// remove un élément du tableau.
		foreach ($this->traduit as $i => $value) {
			if ($value == $terme) {
				unset($this->traduit[$i]);
				$this->traduit = array_values($this->traduit);
			}
		}
	}

	public function addSynonymes($terme)
	{
		$this->synonymes[] = $terme;
	}

	public function getSynonymes()
	{
		return $this->synonymes;
	}

	public function removeSynonymes($terme)
	{
		// remove un élément du tableau.
		foreach ($this->synonymes as $i => $value) {
			if ($value == $terme) {
				unset($this->synonymes[$i]);
				$this->synonymes = array_values($this->synonymes);
			}
		}
	}
}