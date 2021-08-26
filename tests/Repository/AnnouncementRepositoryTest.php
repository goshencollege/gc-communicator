<?php
namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;
use App\Repository\AnnouncementRepository;
use App\Repository\CategoryRepository;
use App\Entity\User;
use App\Entity\Announcement;

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

    $userRepo = static::getContainer()->get(UserRepository::class);
    $testUser = $userRepo->findOneByUsername("david");
    $catRepo = static::getContainer()->get(CategoryRepository::class);
    $testCat = $catRepo->findOneByName("testCategory");

    $countPre = count($testUser->getAnnouncements());

    $announcement = new Announcement();
    $announcement->setSubject('testSubject');
    $announcement->setAuthor('testAuthor');
    $announcement->setCategory($testCat);
    $announcement->setUser($testUser);
    $announcement->setText('testText');
    $announcement->setDate(new \DateTime());
    $this->entityManager->persist($announcement);
    $this->entityManager->flush();

    $this->entityManager->refresh($testUser);
    $countPost = count($testUser->getAnnouncements());
    $this->assertSame($countPre + 1, $countPost);

    $announcement2 = $testUser->getAnnouncements()[$countPost-1];

    //$this->assertNotNull($announcement2);
    //$this->assertSame(++$existing_announcements, count($announcement2));
    // used to be $announcement2[0];
    $this->assertSame($announcement->getSubject(), $announcement2->getSubject());
    $this->assertSame($announcement->getAuthor(), $announcement2->getAuthor());
    $this->assertSame($announcement->getText(), $announcement2->getText());
    //$this->assertSame($announcement->getUser(), $announcement2->getUser());
    //$this->assertSame($announcement->getDate(), $announcement2->getDate());
  }

  /**
   * testing date filter feature
   * 
   * @todo create 3 announcement rows, past, present, and future - check that the present row
   * sets to current date and the others aren't pulled/queried
   * 
   * @author Daniel Boling
   */
  public function dateTest(ObjectManager $manager): void
  {
    $userRepo = static::getContainer()->get(UserRepository::class);
    $testUser = $userRepo->findOneByUsername("david");
    $catRepo = static::getContainer()->get(CategoryRepository::class);
    $testCat = $catRepo->findOneByName("testCategory");

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement = new Announcement();
    $announcement_date = new \DateTime('2021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory($testCat);
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_date = new \DateTime('now');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory($testCat);
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_date = new \DateTime('3021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory($testCat);
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $manager->flush();

    $announcement = $this->entityManager
      ->getRepository(AnnouncementRepository::class)
      ->findToday();

    $this->assertSame(1, $announcement->count());

  }

  /**
   * testing user filter feature
   * 
   * @todo create 2 announcements with two different users, then test that the 
   * created findByUser() function works properly by returning only the announcement(s) created with that user.
   * 
   * @author Daniel Boling 
   */
  public function userTest(ObjectManager $manager): void
  {
    $userRepo = static::getContainer()->get(UserRepository::class);
    $testUser = $userRepo->findOneByUsername("david");
    $catRepo = static::getContainer()->get(CategoryRepository::class);
    $testCat = $catRepo->findOneByName("testCategory");

    $announcement = new Announcement();
    $announcement_date = new \DateTime('now');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory('testCategory');
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $testUser = $userRepo->findOneByUsername("dboling");

    $announcement = new Announcement();
    $announcement_date = new \DateTime('now');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory($testCat);
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $testUser = $userRepo->findOneByUsername("david");
    // Switching back to "cause possible issues"

    $manager->flush();

    $announcement = $testUser->getAnnouncements();

    $this->assertSame($testUser, $announcement->getUser());
  }

  /**
   * Testing category creation and deactiviation.
   * 
   * @todo Create 2 categories, one active, one inactive, and test for 1 active.
   * @author Daniel Boling
   * 
   */
  public function categoryTest(ObjectManager $manager): Void
  {
    $cat = new Category();
    $cat->setName('testCategory1');
    $cat->setActive(1);
    $manager->persist($cat);

    $cat = new Category();
    $cat->setName('testCategory2');
    $cat->setActive(0);
    $manager->persist($cat);

    $manager->flush();

    $cat = $this->entityManager
      ->getRepository(Category::class)
      ->findActive();

    $this->assertSame(1, $cat->count());

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
