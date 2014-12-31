<?php

namespace ProjetBDD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Concept;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;

class ConceptController extends Controller
{
    public function modificationAction($nom)
    {
        $crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
        $request = $this->getRequest();

        $concept = $crudConcept->getByNom($nom);
        $tabConcept = $crudConcept->getAll();

        if ($request->getMethod() == 'POST')
        {
        	if (isset($_POST['description']))
        	{
        		$concept->setDescription($request->request->get('description'));

        		$crudConcept->update($concept);

        		return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'tabConcept' => $tabConcept, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
        	}
        	elseif (isset($_POST['generaliseAction']))
        	{
        		$this->modifierGeneralise($request, $concept, $crudConcept, $tabConcept);
        	}
        	elseif (isset($_POST['specialise']))
        	{
        		$this->modificationSpecialise($request, $concept, $crudConcept, $tabConcept);
        	}
        }

       	return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'tabConcept' => $tabConcept));
    }

    public function modifierGeneralise($request, $concept, $crudConcept, $tabConcept)
    {
    	$tabC = $concept->getGeneralise();
    	$concept->freeGeneralise();
    	$tabGeneralise = $request->request->get('generalise');
    	if (!empty($tabGeneralise))
    	{
    		foreach ($tabGeneralise as $g)
    		{
    			$c = $crudConcept->getByNom($g);
    			$c->addSpecialise($concept->getNomConcept());

    			$crudConcept->update($c);

    			$concept->addGeneralise($g);
    		}
    	}

    	foreach ($tabC as $g)
    	{
    		if (!in_array($g, $concept->getGeneralise()))
    		{
    			$c = $c = $crudConcept->getByNom($g);
    			$c->removeSpecialise($concept->getNomConcept());

    			$crudConcept->update($c);
    		}
    	}

    	$crudConcept->update($concept);

    	return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'tabConcept' => $tabConcept, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }
}
