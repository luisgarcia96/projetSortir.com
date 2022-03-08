<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * sert à recrypter le mdp
     */
    public function upgradePassword(UserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Utilisateur) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        //le user récupère le mdp haché
        $user->setPassword($newHashedPassword);
        //Permet de mettre le user à jour
        $this->_em->persist($user);
        $this->_em->flush();
    }
    //fonction pour modifier les données de l'utilisateur (sauf le mdp)
    public function upgradeUser(Utilisateur $user): void
    {
        //équivalent à une requête SQL
        $modifications = $this->createQueryBuilder('m');
        //
        $modifications->update(Utilisateur::class, 'm');
        //var_dump($user->getId());

        $modifications->set('m.pseudo','?2'); //'?a' param1
        $modifications->set('m.telephone','?3');
        $modifications->set('m.email','?4');

        //WHERE = FILTRE DE TA REQUETE
        $modifications->where('m.id= ?1');//'?b' param2 filtre pour changer les params d'1 utilisateur par l'id

        $modifications->setParameter(1,$user->getId());
        $modifications->setParameter(2,$user->getPseudo());// parma1 c'est user->getPseudo de ta form
        $modifications->setParameter(3,$user->getTelephone());
        $modifications->setParameter(4,$user->getEmail());

        //
        $exec =$modifications->getQuery();
        $exec->execute();
    }
    //fonction pour modifier le mdp
    public function upgradeMDP(Utilisateur $user): void
    {
        $modifications = $this->createQueryBuilder('m');
        $modifications->update(Utilisateur::class, 'm');
        //var_dump($user->getId());

        $modifications->set('m.password','?5');
        //WHERE = FILTRE DE TA REQUETE
        $modifications->where('m.id= ?1');//'?b' param2 filtre pour changer les params d'1 utilisateur par l'id

        $modifications->setParameter(1,$user->getId());
        $modifications->setParameter(5,$user->getPassword());

        $exec =$modifications->getQuery();
        $exec->execute();
    }


    // /**
    //  * @return Utilisateur[] Returns an array of Utilisateur objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}
