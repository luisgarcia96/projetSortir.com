<?php

namespace App\Repository;

use App\Data\InfoRecherche;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function findCreatedSortie(){
        //Requête DQL
        $entityManager = $this->getEntityManager();
        $dql = "
                SELECT s
                FROM App\Entity\Sortie s
                ORDER BY s.id DESC
                ";
        $query = $entityManager->createQuery($dql);
        $query->setMaxResults(1);
        return $query->getOneOrNullResult();
    }


    /**
     * Cette fontction récupère les sorties reliées à une recherche
     * @return Sortie[]
     */
    public function findSortie(InfoRecherche $infoRecherche, Utilisateur $user) : array {

        $queryBuilder = $this->createQueryBuilder('s');

        $queryBuilder->select('s');

        //Filtrage par campus
        if ($infoRecherche->campus != 0) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.campus = :idCampus')
                ->setParameter('idCampus', $infoRecherche->campus);
        }

        //Si l'utilisateur rentre quelque chose dans la barre de recherche
        if (!empty($infoRecherche->motCle)) {
            $queryBuilder = $queryBuilder
                ->andWhere('s.nom LIKE :motCle')
                ->setParameter('motCle', "%{$infoRecherche->motCle}%");
        }

        //Si l'utilisateur saisit des dates
        if (!empty($infoRecherche->dateDebut) or !empty($infoRecherche->dateFin)) {

            $queryBuilder = $queryBuilder
                ->andWhere('s.dateHeureDebut > :dateMin')
                ->setParameter('dateMin', $infoRecherche->dateDebut)
                ->andWhere('s.dateLimiteInscription < :dateMax')
                ->setParameter('dateMax', $infoRecherche->dateFin);
        }

        //Si l'utilisateur coche la case d'organisateur
        if ($infoRecherche->estOrganisateur == true) {

            $queryBuilder = $queryBuilder
                ->andWhere('s.organisateur = :idUser')
                ->setParameter('idUser', $user->getId());
        }

        //Si l'utilisateur coche la case je suis inscrit
        if ($infoRecherche->estInscrit == true) {

            //Fonctionnalité "S'inscrire" à faire
        }

        //Si l'utilisateur coche la case sorties passées
        if ($infoRecherche->estPassee == true) {

            $queryBuilder = $queryBuilder
                ->andWhere('s.dateHeureDebut <= :now')
                ->setParameter('now', (new \DateTime())->format('Y-m-d H:i:s'));
        }


        //dd($queryBuilder);
        $queryBuilder->orderBy('s.dateHeureDebut', 'DESC');
        $query = $queryBuilder->getQuery();

        $query->setMaxResults(10);
        $results = $query->getResult();

        return $results;
    }

    public function findSortieToCancel(int $id){
        //Requête QueryBuilder
        $queryBuilder = $this->createQueryBuilder('s');
        $queryBuilder->join("s.campus", "c");
        $queryBuilder->join("s.lieu", "l");
        $queryBuilder->join("l.ville", "v");
        $queryBuilder->addSelect('c', 'l', 'v');
        $queryBuilder->where("s.id = :id");
        $queryBuilder->setParameter('id', $id);
        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }


}
