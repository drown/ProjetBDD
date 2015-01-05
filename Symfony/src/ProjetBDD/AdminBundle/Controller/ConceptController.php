<?php

/**
 * @file ConceptController.php
 * @brief Controleur d'administration des Concepts, création, modification, suppression
 * @author Brice V.
 * @class ConceptController
 */

namespace ProjetBDD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Concept;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;

class ConceptController extends Controller
{ 
    /*
        @author Brice V.
        @action Creer un concept
        @params Nom du concept, description
        @return Sucess / Fail
    */
    public function creerAction()
    {
        $crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
        $crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST')
        {
            $concept = new Concept;
            $concept->setNomConcept($request->request->get('nomConcept'));
            $concept->setDescription($request->request->get('description'));

            $termeVedette = new TermeVedette;
            $termeVedette->setNomTerme($request->request->get('nomConcept'));
            $termeVedette->setDescription($request->request->get('descriptionTermeVedette'));
            $termeVedette->setConcept($concept);

            if ($crudConcept->getByNom($concept->getNomConcept()) != null)
                return $this->render('ProjetBDDAdminBundle:Concept:creation.html.twig', array('flash' => 'Concept déjà existant !', 'typeFlash' => 'danger'));

            if ($crudTermeVedette->getByNom($termeVedette->getNomTerme()) != null)
                return $this->render('ProjetBDDAdminBundle:Concept:creation.html.twig', array('flash' => 'Terme déjà existant !', 'typeFlash' => 'danger'));

            $crudConcept->creer($concept);
            $crudTermeVedette->creer($termeVedette);

            return $this->render('ProjetBDDAdminBundle:Concept:creation.html.twig', array('flash' => 'Création effectuée !', 'typeFlash' => 'success'));
        }
        else
        {
            return $this->render('ProjetBDDAdminBundle:Concept:creation.html.twig', array());
        }
    }

    /*
        @author Brice V.
        @action Modifier un concept, description, generalisation, specialisation
        @params nom d'un concept
        @return sucess / fail
    */
    public function modificationAction($nom)
    {
        $crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
        $crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');
        $request = $this->getRequest();

        $concept = $crudConcept->getByNom($nom);
        $termeVedette = $crudTermeVedette->getByConcept($concept);
        $tabConcept = $crudConcept->getAll();

        if ($request->getMethod() == 'POST')
        {
        	if (isset($_POST['description']))
        	{
        		$concept->setDescription($request->request->get('description'));

        		$crudConcept->update($concept);

        		return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'termeVedette' => $termeVedette, 'tabConcept' => $tabConcept, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
        	}
        	elseif (isset($_POST['generaliseAction']))
        	{
        		return $this->modifierGeneralise($request, $concept, $crudConcept, $tabConcept, $termeVedette);
        	}
        	elseif (isset($_POST['specialiseAction']))
        	{
        		return $this->modifierSpecialise($request, $concept, $crudConcept, $tabConcept, $termeVedette);
        	}
        }
        //asort($tabConcept);
        
       	return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'tabConcept' => $tabConcept, 'termeVedette' => $termeVedette));
    }

    /*
        @author Brice V.
        @action Modifie la liste des generalisations
        @params Concept a modifier, tableau des concepts a mettre a jour
        @return Sucess / fail
    */
    public function modifierGeneralise($request, $concept, $crudConcept, $tabConcept, $termeVedette)
    {
    	$tabC = $concept->getGeneralise();
    	$concept->freeGeneralise();
    	$tabGeneralise = $request->request->get('generalise');
    	if (!empty($tabGeneralise))
    	{
    		foreach ($tabGeneralise as $g)
    		{
    			$c = $crudConcept->getByNom($g);

                if (!in_array($g, $tabC))
                {
                    $c->addSpecialise($concept->getNomConcept());

                    $crudConcept->update($c);
                }

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
        //asort($tabConcept);
    	return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'termeVedette' => $termeVedette, 'tabConcept' => $tabConcept, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

    /*
        @author Brice V.
        @action Modifie la liste des specialisations
        @params Concept a modifier, tableau des concepts a mettre a jour
        @return Success / Fail
    */
    public function modifierSpecialise($request, $concept, $crudConcept, $tabConcept, $termeVedette)
    {
        $tabC = $concept->getSpecialise();
        $concept->freeSpecialise();
        $tabSpecialise = $request->request->get('specialise');
        if (!empty($tabSpecialise))
        {
            foreach ($tabSpecialise as $g)
            {
                $c = $crudConcept->getByNom($g);

                if (!in_array($g, $tabC))
                {
                    $c->addGeneralise($concept->getNomConcept());

                    $crudConcept->update($c);
                }

                $concept->addSpecialise($g);
            }
        }

        foreach ($tabC as $g)
        {
            if (!in_array($g, $concept->getSpecialise()))
            {
                $c = $c = $crudConcept->getByNom($g);
                $c->removeGeneralise($concept->getNomConcept());

                $crudConcept->update($c);
            }
        }

        $crudConcept->update($concept);
        //asort($tabConcept);
        return $this->render('ProjetBDDAdminBundle:Concept:modification.html.twig', array('concept' => $concept, 'termeVedette' => $termeVedette, 'tabConcept' => $tabConcept, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

    /*
        @author Brice V.
        @action Suprime un concept
        @params Nom du concept
        @return redirection d'url
    */
    public function supprimerAction($nom)
    {
        $crudConcept = $this->container->get('ProjetBDD.CRUD.Concept');
        $crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');

        $concept = $crudConcept->getByNom($nom);

        if ($concept == null)
            return $this->redirect($this->generateUrl('ProjetBDDAdminIndexConcept'));

        $termeVedette = $crudTermeVedette->getByConcept($concept);

        $crudTermeVedette->supprimer($termeVedette);
        $crudConcept->supprimer($concept);

        return $this->redirect($this->generateUrl('ProjetBDDAdminIndexConcept'));
    }
}
