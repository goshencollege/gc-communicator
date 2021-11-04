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
  private function query_today($date_input = 'now', $approval = 1)
  {

    $date = new \DateTime($date_input, new \DateTimeZone('GMT'));

    $qb = $this->createQueryBuilder('a')
      ->Where('a.start_date <= :date AND a.end_date >= :date');
    // if approval == 1, filter for approval. if approval != 1, skip this filter and return all for today
    if ($approval == 1)
    {
      $qb->andWhere('a.approval = 1');
    }
    $qb->setParameter('date', $date->format('Y-m-d'))
      ->orderBy('a.id', 'ASC')
    ;
    return $qb->getQuery();

  }

  /**
   * Method using query_today to get a result array.
   * Requirements: date match.
   * 
   * @author Daniel Boling
   */
  public function find_today($date_input = 'now', $approval = 1)
  {

    return $this->query_today($date_input, $approval)
      ->getResult()
    ;

  }

    /**
   * Method using query_today to get a countable array.
   * 
   * @author Daniel Boling
   */
  public function count_today($date_input = 'now', $approval = 1)
  {

    return $this->query_today($date_input, $approval)
      ->getScalarResult()
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
