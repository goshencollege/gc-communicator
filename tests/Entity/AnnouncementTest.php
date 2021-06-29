<?php
namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\Announcement;

final class AnnouncementTest extends TestCase 
{
    /**
     * Test creating a new Article then verify it's info.
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
