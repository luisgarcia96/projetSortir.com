<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublierSortieController extends AbstractController
{
    /**
     * @Route ("/publish" , name="publierSortie_publish")
     */
    public function publish(
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository
    ): Response{
        $sortie = new Sortie();

        //On récupère la dernière sortie enregistrée par son id
        $sortie = $sortieRepository->findCreatedSortie();


        //Changement de l'état de la sortie
        $etat = new Etat();
        $etat = $etatRepository->findOneBy(['id'=>2]);
        $sortie->setEtat($etat);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($sortie);
        $manager->flush();


        return $this->redirectToRoute('main_accueil');

    }
}
