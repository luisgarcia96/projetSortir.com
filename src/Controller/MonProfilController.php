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

        //si on appui sur enregistrer
        if($profilForm->isSubmitted() && $profilForm->isValid() )
        {

          /*  dd($profilForm->get('password')->getData());
           if ($profilForm->get('password')->getData())
           {
               // encode the plain password
               $user->setPassword(
                        $passwordEncoder->encodePassword($user, $user->getPassword()
                        ));
                    //rentre le mdp haché dans l'utilisateurRepository afin d'être envoyé dans la base de données
                    $utilisateurRepository->upgradeMDP($user);

               //var_dump($userForm);
            }*/

           $entityManager->persist($user);
           $entityManager->flush();


            //message pour dire que le profil est modifié
            $this->addFlash('success', 'Votre profil à bien été modifié !');

            /* if ($photo = $profilForm['photo']->getData())
              {
                  //Il enregistre la photo dans un fichier
                  //le random_bytes sert pour donner un nom aléatoire au fichier
                  $filename = bin2hex(random_bytes(6)).'.'.$photo->guessExtension();
                                  try {
                                      $photo->move($photoDir, $filename);
                                  } catch (FileException $e) {
                                     // unable to upload the photo, give up
                                  }
              }*/

        }
        else
        {
            //message pour dire que le profil est modifié
            $this->addFlash('error', 'Votre profil ne peux être modifié !');
        }
        return $this->render('main/profil.html.twig', [
            'profilForm'=> $profilForm->createView(),
            'user'=> $user
        ]);

    }

}
