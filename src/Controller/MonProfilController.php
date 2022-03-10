<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\GererMonProfilType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MonProfilController extends AbstractController
{
    /**
     * @Route("/mon/profil", name="mon_profil")
     */
    public function index(): Response
    {
        return $this->render('mon_profil/cancel.html.twig', [
            'controller_name' => 'MonProfilController',
        ]);
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function profil(Request $request, EntityManagerInterface $entityManager, string $photoDir,
                           UtilisateurRepository $utilisateurRepository, UserPasswordEncoderInterface $passwordEncoder):Response
    {
        //Récupère les données de l'utilisateur dans la base de données
        $user = $this->getUser();


        //On affiche les données dans le formulaire pour modifier le profil
        $profilForm = $this->createForm(GererMonProfilType::class, $user );

        //Traitement du formulaire
        $profilForm->handleRequest($request);
        if($profilForm->isSubmitted() && $profilForm->isValid() )
        {

           if ($profilForm->get('password')->getData())
           {
               // Encodage du mot de passe
               $user->setPassword(
                    $passwordEncoder->encodePassword($user, $user->getPassword())
               );
                    $utilisateurRepository->upgradeMDP($user);
            }

           $entityManager->persist($user);
           $entityManager->flush();


            //Confirmation de modification
            $this->addFlash('success', 'Votre profil à bien été modifié !');
        }
        else
        {
            //Erreur lors de la modification
            $this->addFlash('error', 'Votre profil ne peux être modifié !');
        }
        return $this->render('main/profil.html.twig', [
            'profilForm'=> $profilForm->createView(),
            'user'=> $user
        ]);

    }

}
