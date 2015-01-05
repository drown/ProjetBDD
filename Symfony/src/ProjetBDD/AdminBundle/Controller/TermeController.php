<?php

/**
 * @file TermeController.php
 * @brief Controleur d'administration des Termes, création, modification, suppression
 * @author Brice V.
 * @class TermeController
 */

namespace ProjetBDD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Terme;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;



class TermeController extends Controller
{ 
    /*
    @author Brice V.
    @action creer un terme via panneau admin
    @param nom et description du terme
    @return Sucess / fail
    */
    public function creerAction()
    {
        $crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
        $request = $this->getRequest();

        if ($request->getMethod() == 'POST')
        {
            $terme = new Terme;
            $terme->setNomTerme($request->request->get('nomTerme'));
            $terme->setDescription($request->request->get('description'));

            if ($crudTerme->getByNom($terme->getNomTerme()) != null)
                return $this->render('ProjetBDDAdminBundle:Terme:creation.html.twig', array('flash' => 'Terme déjà existant !', 'typeFlash' => 'danger'));

            $crudTerme->creer($terme);

            return $this->render('ProjetBDDAdminBundle:Terme:creation.html.twig', array('flash' => 'Création effectuée !', 'typeFlash' => 'success'));
        }
        else
        {
            return $this->render('ProjetBDDAdminBundle:Terme:creation.html.twig', array());
        }
    }

    /*
    @author Brice V.
    @action modifie un terme (associe / traduit ou synonyme)
    @param nom du terme
    @return Sucess / fail
    */
    public function modificationAction($nom)
    {
        $crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
        $crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');
        $request = $this->getRequest();

        $terme = $crudTerme->getByNom($nom);
        $tabTermeS = $crudTerme->getAll();
        $tabTermeV = $crudTermeVedette->getAll();

        $tabTerme = array_merge($tabTermeS, $tabTermeV);

        usort($tabTerme, function ($a, $b)
{   
        return strnatcasecmp($a->getNomTerme(), $b->getNomTerme());
});

        if ($request->getMethod() == 'POST')
        {
        	if (isset($_POST['description']))
        	{
        		$terme->setDescription($request->request->get('description'));

        		$crudTerme->update($terme);
                //asort($tabTerme);
                //asort($tabTermeV);
        		return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
        	}
        	elseif (isset($_POST['associeAction']))
        	{
        		return $this->modifierAssocie($request, $terme, $crudTerme, $tabTerme, $tabTermeV);
        	}
        	elseif (isset($_POST['traduitAction']))
        	{
        		return $this->modifierTraduit($request, $terme, $crudTerme, $tabTerme, $tabTermeV);
        	}
            elseif (isset($_POST['synonymesAction']))
            {
                return $this->modifierSynonymes($request, $terme, $crudTerme, $tabTerme, $tabTermeV);
            }
        }
        //asort($tabTerme);
        //asort($tabTermeV);
       	return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV));
    }
    /*
    @author Brice V.
    @action modifie un terme (associe)
    @param nom du terme
    @return Sucess / fail
    */
    public function modifierAssocie($request, $terme, $crudTerme, $tabTerme, $tabTermeV)
    {
    	$tabT = $terme->getAssocie();
    	$terme->freeAssocie();
    	$tabAssocie = $request->request->get('associe');

    	if (!empty($tabAssocie))
    	{
    		foreach ($tabAssocie as $g)
    		{
    			$c = $crudTerme->getByNom($g);

                if (!in_array($g, $tabT))
                {
                    $c->addAssocie($terme->getNomTerme());
                    $crudTerme->update($c);
                }

    			$terme->addAssocie($g);
    		}
    	}

    	foreach ($tabT as $g)
    	{
    		if (!in_array($g, $terme->getAssocie()))
    		{
    			$c = $crudTerme->getByNom($g);
    			$c->removeAssocie($terme->getNomTerme());

    			$crudTerme->update($c);
    		}
    	}

    	$crudTerme->update($terme);
        //asort($tabTerme);
        //asort($tabTermeV);
    	return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

    /*
    @author Brice V.
    @action modifie un terme (traduit)
    @param nom du terme
    @return Sucess / fail
    */
    public function modifierTraduit($request, $terme, $crudTerme, $tabTerme, $tabTermeV)
    {
        $tabT = $terme->getTraduit();
        $terme->freeTraduit();
        $tabTraduit = $request->request->get('traduit');

        if (!empty($tabTraduit))
        {
            foreach ($tabTraduit as $g)
            {
                $c = $crudTerme->getByNom($g);  

                if (!in_array($g, $tabT))
                {
                    $c->addTraduit($terme->getNomTerme());

                    $crudTerme->update($c);
                }

                $terme->addTraduit($g);
            }
        }

        foreach ($tabT as $g)
        {
            if (!in_array($g, $terme->getTraduit()))
            {
                $c = $crudTerme->getByNom($g);
                $c->removeTraduit($terme->getNomTerme());

                $crudTerme->update($c);
            }
        }

        $crudTerme->update($terme);
        //asort($tabTerme);
        //asort($tabTermeV);
        return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

    /*
    @author Brice V.
    @action modifie un terme (Synonymes)
    @param nom du terme
    @return Sucess / fail
    */
    public function modifierSynonymes($request, $terme, $crudTerme, $tabTerme, $tabTermeV)
    {
        $crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');

        $tabT = $terme->getSynonymes();
        $terme->freeSynonymes();
        $tabSynonymes = $request->request->get('synonymes');

        if (!empty($tabSynonymes))
        {
            foreach ($tabSynonymes as $g)
            {
                if ($terme instanceof TermeVedette)
                {
                    $c = $crudTermeVedette->getByNom($g);

                    if (!in_array($g, $tabT))
                    {
                        $c->addSynonymes($terme->getNomTerme());

                        $crudTermeVedette->update($c);
                    }   
                }

                $terme->addSynonymes($g);
            }
        }

        if ($terme instanceof TermeVedette)
        { 
            foreach ($tabT as $g)
            {
                if (!in_array($g, $terme->getSynonymes()))
                {
                    $c = $crudTermeVedette->getByNom($g);
                    $c->removeSynonymes($terme->getNomTerme());

                    $crudTermeVedette->update($c);
                }
            }

        }

        $crudTermeVedette->update($terme);
        //asort($tabTerme);
        //asort($tabTermeV);
        return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

    /*
    @author Brice V.
    @action supprime un terme 
    @param nom du terme
    @return redirection d'url
    */
    public function supprimerAction($nom)
    {
        $crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');

        $terme = $crudTerme->getByNom($nom);

        if ($terme == null || $terme instanceof TermeVedette)
            return $this->redirect($this->generateUrl('ProjetBDDAdminIndexTermes'));

        $crudTerme->supprimer($terme);

        return $this->redirect($this->generateUrl('ProjetBDDAdminIndexTerme'));
    }
}
