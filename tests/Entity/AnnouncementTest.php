<?php
namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Announcement;
use App\Entity\User;

final class AnnouncementTest extends TestCase 
{
  /**
   * Test creating a new announcement then verify it's info.
   * 
   * @author David King
   * 
   */
  public function testCreate(): void
  {
    $announcement = new Announcement();
    $announcement->setSubject('testSubject');
    $announcement->setAuthor('testAuthor');
    $announcement->setText('testText');

    $this->assertSame('testSubject', $announcement->getSubject());
    $this->assertSame('testAuthor', $announcement->getAuthor());
    $this->assertSame('testText', $announcement->getText());
  }

  public function dateTest(ObjectManager $manager): void
  {
    $user = new User();
    $user->setUsername('david');
    $user->setPassword($this->passwordHasher->hashPassword(
      $user,
      '12345'
    ));
    $manager->persist($user);

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement = new Announcement();
    $announcement_date = new \DateTime('2021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setUser($user);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_date = new \DateTime('now');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setUser($user);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_date = new \DateTime('3021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setUser($user);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $manager->flush();
    
   }
}

// EOF
