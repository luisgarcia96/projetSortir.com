<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController {

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route ("/create" , name="sortie_create")
     */
    public function create(
        Request $request,
        EtatRepository $etatRepository,
        SortieRepository $sortieRepository,
        UtilisateurRepository $utilisateurRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Hydrater les propriétés absentes du formulaire
            $etat = new Etat();
            $etat = $etatRepository->findOneBy(['id'=>1]);
            $sortie->setEtat($etat);

            $this->creationSortie($utilisateurRepository, $sortie, $entityManager);

            //Affichage d'un message de succès
            $this->addFlash('success', 'La sortie a bien été ajoutée !');

            //Redirection vers la page d'accueil
            return $this->redirectToRoute('main_accueil');
        }

        //Affichage du formulaire
        return $this->render('sortie/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param UtilisateurRepository $utilisateurRepository
     * @param Sortie $sortie
     * @param EntityManagerInterface $entityManager
     */
    public function creationSortie(UtilisateurRepository $utilisateurRepository, Sortie $sortie, EntityManagerInterface $entityManager): void
    {
        $utilisateur = new Utilisateur();
        $idUser = $this->getUser()->getId();
        //if($utilisateur->getRoles('ROLE_ORGANISATEUR')){
        $utilisateur = $utilisateurRepository->findOneBy(['id' => $idUser]);
        $sortie->setOrganisateur($utilisateur);
        // }

        //Sauvegarder en bdd
        $entityManager->persist($sortie);
        $entityManager->flush();
    }
}