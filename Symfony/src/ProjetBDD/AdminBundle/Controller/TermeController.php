<?php

namespace ProjetBDD\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ProjetBDD\GeneralBundle\Entity\Terme;
use ProjetBDD\GeneralBundle\Entity\TermeVedette;

class TermeController extends Controller
{ 
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

    public function modificationAction($nom)
    {
        $crudTerme = $this->container->get('ProjetBDD.CRUD.Terme');
        $crudTermeVedette = $this->container->get('ProjetBDD.CRUD.TermeVedette');
        $request = $this->getRequest();

        $terme = $crudTerme->getByNom($nom);
        $tabTermeS = $crudTerme->getAll();
        $tabTermeV = $crudTermeVedette->getAll();

        $tabTerme = array_merge($tabTermeS, $tabTermeV);

        if ($request->getMethod() == 'POST')
        {
        	if (isset($_POST['description']))
        	{
        		$terme->setDescription($request->request->get('description'));

        		$crudTerme->update($terme);

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

       	return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV));
    }

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
    			$c->addAssocie($terme->getNomTerme());

    			$crudTerme->update($c);

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

    	return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

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
                $c->addTraduit($terme->getNomTerme());

                $crudTerme->update($c);

                $terme->addTraduit($g);
            }
        }

        foreach ($tabT as $g)
        {
            if (!in_array($g, $terme->getAssocie()))
            {
                $c = $crudTerme->getByNom($g);
                $c->removeTraduit($terme->getNomTerme());

                $crudTerme->update($c);
            }
        }

        $crudTerme->update($terme);

        return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

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
                    $c->addSynonymes($terme->getNomTerme());

                    $crudTermeVedette->update($c);
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

        return $this->render('ProjetBDDAdminBundle:Terme:modification.html.twig', array('terme' => $terme, 'tabTerme' => $tabTerme, 'tabTermeV' => $tabTermeV, 'flash' => 'Modification effectuée !', 'typeFlash' => 'success'));
    }

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
