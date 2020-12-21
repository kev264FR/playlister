<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Playlist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Playlist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Playlist[]    findAll()
 * @method Playlist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlaylistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    // /**
    //  * @return Playlist[] Returns an array of Playlist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Playlist
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getAll(){
        return $this->createQueryBuilder('p')
                    ->orderBy('p.createdAt', 'DESC')
                    ->getQuery();
    }


    public function getAllPublic(){
        return $this->createQueryBuilder('p')
                    ->andWhere('p.public = 1')
                    ->orderBy('p.createdAt', 'DESC')
                    ->getQuery();               
    }

    public function getAllPublicNamed($titre){
        return $this->createQueryBuilder('p')
                    ->andWhere('p.public = 1')
                    ->andWhere('p.title LIKE :titre')
                    ->setParameter('titre', "%$titre%")
                    ->getQuery();
    }

    public function getAllNamed($titre){
        return $this->createQueryBuilder('p')
                    ->andWhere('p.title LIKE :titre')
                    ->setParameter('titre', "%$titre%")
                    ->getQuery();
    }

    public function getMostLikedPublic(){
        return $this->createQueryBuilder('p')
                    ->select('p, SIZE(p.likers) as likers')
                    ->where("p.public = 1")
                    ->groupBy('p.id')
                    ->orderBy('likers', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function getMostFollowedPublic(){
        return $this->createQueryBuilder('p')
                    ->select('p, SIZE(p.followers) as followers')
                    ->where("p.public = 1")
                    ->groupBy('p.id')
                    ->orderBy('followers', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function getMostLiked(){
        return $this->createQueryBuilder('p')
                    ->select('p, SIZE(p.likers) as likers')
                    ->groupBy('p.id')
                    ->orderBy('likers', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function getMostFollowed(){
        return $this->createQueryBuilder('p')
                    ->select('p, SIZE(p.followers) as followers')
                    ->groupBy('p.id')
                    ->orderBy('followers', 'DESC')
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getOneOrNullResult();
    }

    public function getAllMyPlaylists($user){
        return $this->createQueryBuilder('p')
                    ->andWhere('p.user = :user')
                    ->setParameter('user', $user)
                    ->orderBy('p.createdAt', 'DESC')
                    ->getQuery();
    }

    public function getUsersPublicPlaylists($user){
        return $this->createQueryBuilder('p')
                    ->andWhere('p.public = 1')
                    ->andWhere('p.user = :user')
                    ->setParameter('user', $user)
                    ->orderBy('p.createdAt', 'DESC')
                    ->getQuery();
    }
    
}
