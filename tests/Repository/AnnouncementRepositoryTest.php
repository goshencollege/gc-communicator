<?php
namespace App\Tests\Repository;

use App\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnnouncementRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Setup the test environment.  This is called automatically by PHPUnit
     * 
     * @author David King
     * 
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Create a new announcement and persist it to the database.  Then retreive
     * all of the announcements from the database and make sure the values match
     * the announcement that was just persisted.
     * 
     * @todo check the number of announcements retreived from the database
     * 
     * @author David King
     * 
     */
    public function testCreate(): void
    {
        $existing_announcements = count(
            $this->entityManager
            ->getRepository(Announcement::class)
            ->findAll());

        $date = new DateTime('')
        $announcement = new Announcement();    
        $announcement->setSubject('testSubject');
        $announcement->setAuthor('testAuthor');
        $announcement->setText('testText');
        $announcement->setUser('testUser');
        $announcement->setDate('2021-01-30');
        $this->entityManager->persist($announcement);
        $this->entityManager->flush();

        $announcement2 = $this->entityManager
            ->getRepository(Announcement::class)
            ->findAll();

        $this->assertNotNull($announcement2);
        $this->assertSame(++$existing_announcements, count($announcement2));
        $this->assertSame($announcement->getSubject(), $announcement2[0]->getSubject());
        $this->assertSame($announcement->getAuthor(), $announcement2[0]->getAuthor());
        $this->assertSame($announcement->getText(), $announcement2[0]->getText());
        $this->assertSame($announcement->getUser(), $announcement2[0]->getUser());
        $this->assertSame($announcement->getDate(), $announcement2[0]->getDate());
    }

    /**
     * Cleanup from the testing.  This is called automatically by PHPUnit
     * 
     * @author David King
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}

//EOF
