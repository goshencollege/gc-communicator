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

}

// EOF
