<?php

namespace App\Repository;

use App\Entity\Announcement;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Announcement|null find($id, $lockMode = null, $lockVersion = null)
 * @method Announcement|null findOneBy(array $criteria, array $orderBy = null)
 * @method Announcement[]    findAll()
 * @method Announcement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnouncementRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Announcement::class);
  }

  /**
   * Function building a query to only return all announcements for the locale date
   * 
   * @author Daniel Boling
   */
  public function findToday()
  {

    $date = new \DateTime('now', new \DateTimeZone('America/Indiana/Indianapolis'));

    return $this->createQueryBuilder('a')
      ->andWhere('a.Date = :date')
      ->setParameter('date', $date->format('Y-m-d'))
      ->orderBy('a.id', 'ASC')
      ->getQuery()
      ->getResult()
    ;

  }

  /**
   * Function building a query to return all announcements from the logged in user
   * 
   * @author Daniel Boling
   */
  public function findByUser($user)
  {

    return $this->createQueryBuilder('a')
      ->andWhere('a.User = :user')
      ->setParameter('user', $user)
      ->getQuery()
      ->getResult()
    ;
  }

  // /**
  //  * @return Announcement[] Returns an array of Announcement objects
  //  */
  /*
  public function findByExampleField($value)
  {
      return $this->createQueryBuilder('a')
          ->andWhere('a.exampleField = :val')
          ->setParameter('val', $value)
          ->orderBy('a.id', 'ASC')
          ->setMaxResults(10)
          ->getQuery()
          ->getResult()
      ;
  }
  */

  /*
  public function findOneBySomeField($value): ?Announcement
  {
      return $this->createQueryBuilder('a')
          ->andWhere('a.exampleField = :val')
          ->setParameter('val', $value)
          ->getQuery()
          ->getOneOrNullResult()
      ;
  }
  */
}

// EOF
