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
   * Custom method to pull all announcements created for the current date
   * 
   * @author Daniel Boling
   */
  private function query_today()
  {

    $date = new \DateTime('now', new \DateTimeZone('America/Indiana/Indianapolis'));

    return $this->createQueryBuilder('a')
      ->Where('a.start_date <= :date AND a.end_date >= :date')
      ->setParameter('date', $date->format('Y-m-d'))
      ->orderBy('a.id', 'ASC')
      ->getQuery()
    ;

  }

  /**
   * Method using query_today to get a result array.
   * Requirements: date match.
   * 
   * @author Daniel Boling
   */
  public function find_today()
  {
    return $this->query_today()
      ->getResult()
    ;
  }

    /**
   * Method using query_today to get a countable array.
   * 
   * @author Daniel Boling
   */
  public function count_today()
  {
    return $this->query_Today()
      ->getScalarResult()
    ;
  }

  /**
   * Method using query_today to get a result array.
   * Requirement: date match. Approved.
   * 
   * @author Daniel Boling
   */
  public function find_today_approved()
  {
    $date = new \DateTime('now', new \DateTimeZone('America/Indiana/Indianapolis'));

    return $this->createQueryBuilder('a')
      ->where('a.start_date <= :date AND a.end_date >= :date')
      ->andWhere('a.approval = 1')
      ->setParameter('date', $date->format('Y-m-d'))
      ->orderBy('a.id', 'ASC')
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
