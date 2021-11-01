<?php
namespace App\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;
use App\Repository\AnnouncementRepository;
use App\Repository\CategoryRepository;
use App\Entity\User;
use App\Entity\Announcement;
use App\Entity\Category;
use phpDocumentor\Reflection\Types\Void_;

class AnnouncementRepositoryTest extends KernelTestCase
{
  /**
   * @var \Doctrine\ORM\EntityManager
   */
  private $em;

  /**
   * Setup the test environment.  This is called automatically by PHPUnit
   * 
   * @author David King
   * 
   */
  protected function setUp(): void
  {
    $kernel = self::bootKernel();

    $this->em = $kernel->getContainer()
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
  public function test_create(): void
  {

    $existing_announcements = count(
      $this->em
      ->getRepository(Announcement::class)
      ->findAll());

    $test_user = $this->em
      ->getRepository(User::class)
      ->findOneByUsername("test_user");

    $test_cat = $this->em
      ->getRepository(Category::class)
      ->findOneByName("test_category");

    $count_pre = count($test_user->getAnnouncements());

    $announcement = new Announcement();
    $announcement->setSubject('test_subject');
    $announcement->setAuthor('test_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setText('test_text');
    $announcement->setApproval(1);
    $announcement->setStartDate(new \DateTime('now', new \DateTimeZone('GMT')));
    $announcement->setEndDate(new \DateTime('now', new \DateTimeZone('GMT')));
    $this->em->persist($announcement);
    $this->em->flush();

    $this->em->refresh($test_user);
    $count_post = count($test_user->getAnnouncements());

    $announcement2 = $test_user->getAnnouncements()[$count_post-1];

    $this->assertSame($count_pre + 1, $count_post);
    $this->assertSame($announcement->getSubject(), $announcement2->getSubject());
    $this->assertSame($announcement->getAuthor(), $announcement2->getAuthor());
    $this->assertSame($announcement->getCategory(), $announcement2->getCategory());
    $this->assertSame($announcement->getUser(), $announcement2->getUser());
    $this->assertSame($announcement->getText(), $announcement2->getText());
    $this->assertSame($announcement->getStartDate(), $announcement2->getStartDate());
    $this->assertSame($announcement->getEndDate(), $announcement2->getEndDate());

  }

  /**
   * testing date filter feature
   * 
   * @todo create 3 announcement rows, past, present, and future - check that the present row
   * sets to current date and the others aren't pulled/queried
   * 
   * @author Daniel Boling
   */
  public function test_date(): void
  {
    $test_user = $this->em
      ->getRepository(User::class)
      ->findOneByUsername("test_user");

    $test_cat = $this->em
      ->getRepository(Category::class)
      ->findOneByName("test_category");

    $pre_announcement = $this->em
      ->getRepository(Announcement::class)
      ->count_today();

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement = new Announcement();
    $announcement_past_date = new \DateTime('-1 week', new \DateTimeZone('GMT'));
    $announcement->setSubject('test_subject');
    $announcement->setAuthor('test_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_past_date);
    $announcement->setEndDate($announcement_past_date);
    $announcement->setText('test_text');
    $announcement->setApproval(1);
    $this->em->persist($announcement);

    $announcement = new Announcement();
    $announcement_today_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement->setSubject('test_subject');
    $announcement->setAuthor('test_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_today_date);
    $announcement->setEndDate($announcement_today_date);
    $announcement->setText('test_text');
    $announcement->setApproval(1);
    $this->em->persist($announcement);

    $announcement = new Announcement();
    $announcement_future_date = new \DateTime('+1 week', new \DateTimeZone('GMT'));
    $announcement->setSubject('test_subject');
    $announcement->setAuthor('test_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_future_date);
    $announcement->setEndDate($announcement_future_date);
    $announcement->setText('test_text');
    $announcement->setApproval(1);
    $this->em->persist($announcement);

    $this->em->flush();

    $post_announcement = $this->em
      ->getRepository(Announcement::class)
      ->count_today();

    $this->assertSame(count($post_announcement), (count($pre_announcement)+1));
    // checking that the count of announcements with current date prior to addition + 1
    // is the same as the count of announcements with current date after addition

  }

  /**
   * testing user filter feature
   * 
   * @todo create 2 announcements with two different users, then test that the 
   * created findByUser() function works properly by returning only the announcement(s) created with that user.
   * 
   * @author Daniel Boling
   */
  public function test_user(): void
  {
    $test_user = $this->em
      ->getRepository(User::class)
      ->findOneByUsername("test_user");

    $test_cat = $this->em
      ->getRepository(Category::class)
      ->findOneByName("test_category");

    $announcement = new Announcement();
    $announcement_today_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement->setSubject('auto_subject');
    $announcement->setAuthor('auto_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_today_date);
    $announcement->setEndDate($announcement_today_date);
    $announcement->setText('auto_text');
    $announcement->setApproval(1);
    $this->em->persist($announcement);

    $test_user = $this->em
      ->getRepository(User::class)
      ->findOneByUsername("fixture_user");

    $announcement = new Announcement();
    $announcement_today_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement->setSubject('test_subject');
    $announcement->setAuthor('test_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_today_date);
    $announcement->setEndDate($announcement_today_date);
    $announcement->setText('test_text');
    $announcement->setApproval(1);
    $this->em->persist($announcement);

    $this->em->flush();

    $test_user = $this->em
      ->getRepository(User::class)
      ->findOneByUsername("test_user");

    $count_post = count($test_user->getAnnouncements());

    $announcement_user = $test_user->getAnnouncements()[$count_post-1];

    $this->assertSame($test_user, $announcement_user->getUser());
    // this is a bad test - needs different steps

  }

  /**
   * Testing category creation and deactiviation.
   * 
   * @todo Create 2 categories, one active, one inactive, and test for 1 active.
   * @author Daniel Boling
   * 
   */
  public function test_category(): Void
  {

    $cat_pre_count = count($this->em
      ->getRepository(Category::class)
      ->find_active())
    ;

    $manager = $this->em;
    $cat = new Category();
    $cat->setName('testCategory1');
    $cat->setActive(1);
    $manager->persist($cat);

    $cat = new Category();
    $cat->setName('testCategory2');
    $cat->setActive(0);
    $manager->persist($cat);

    $manager->flush();

    $cat_post_count = count($this->em
      ->getRepository(Category::class)
      ->find_active())
    ;

    $this->assertSame(($cat_pre_count+1), $cat_post_count);

  }

  /**
   * Testing announcement recurrence.
   * 
   * @todo Create 1 announcement with 3 days to "cycle through", then check with 5 dates, from before
   * to after the recurrence date. Only 2 announcements should return for "yesterday" and "tomorrow", with the
   * 6 returning for "today". There should be no fixtures for two days prior/after.
   * @author Daniel Boling
   */
  public function test_recurrence(): Void
  {

    $test_user = $this->em
      ->getRepository(User::class)
      ->findOneByUsername("test_user");

    $test_cat = $this->em
      ->getRepository(Category::class)
      ->findOneByName("test_category");

    $pre_announcement = $this->em
      ->getRepository(Announcement::class)
    ;

    $announcement = new Announcement();
    $announcement_start_date = new \DateTime('yesterday', new \DateTimeZone('GMT'));
    $announcement_end_date = new \DateTime('tomorrow', new \DateTimeZone('GMT'));
    $announcement->setSubject('test_subject');
    $announcement->setAuthor('test_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_start_date);
    $announcement->setEndDate($announcement_end_date);
    $announcement->setText('test_text');
    $announcement->setApproval(1);
    $this->em->persist($announcement);

    $this->em->flush();

    $post_announcement = $this->em
      ->getRepository(Announcement::class)
    ;

    $this->assertSame(count($pre_announcement->count_today('-2 days')), count($post_announcement->count_today('-2 days')));
    // ensure the announcement will not fire 2 days before
    $this->assertSame((count($pre_announcement->count_today('yesterday'))+1), count($post_announcement->count_today('yesterday')));
    $this->assertSame((count($pre_announcement->count_today('now'))+1), count($post_announcement->count_today('now')));
    $this->assertSame((count($pre_announcement->count_today('tomorrow'))+1), count($post_announcement->count_today('tomorrow')));
    $this->assertSame(count($pre_announcement->count_today('+2 days')), count($post_announcement->count_today('+2 days')));
    // ensure the announcement will not fire 2 days after

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
    $this->em->close();
    $this->em = null;
  }
}

//EOF
