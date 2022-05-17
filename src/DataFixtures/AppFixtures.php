<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Announcement;
use App\Entity\User;
use App\Entity\Category;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class AppFixtures extends Fixture
{
  private $passwordHasher;

  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;

  }

  public function load(ObjectManager $manager)
  {

    $user = new User();
    $user->setUsername('david');
    $user->setRoles(['ROLE_ADMIN']);
    $user->setPassword($this->passwordHasher->hashPassword(
        $user,
        '12345'
    ));
    $manager->persist($user);

    $user = new User();
    $user->setUsername('dboling');
    $user->setRoles(['ROLE_ADMIN']);
    $user->setPassword($this->passwordHasher->hashPassword(
        $user,
        '12345'
    ));
    $manager->persist($user);

    $test_user = new User();
    $test_user->setUsername('test_user');
    $test_user->setRoles(['ROLE_USER']);
    $test_user->setPassword($this->passwordHasher->hashPassword(
        $test_user,
        '12345'
    ));
    $manager->persist($test_user);


    $test_user = new User();
    $test_user->setUsername('fixtures_user');
    $test_user->setRoles(['ROLE_USER']);
    $test_user->setPassword($this->passwordHasher->hashPassword(
        $test_user,
        '12345'
    ));
    $manager->persist($test_user);

    $test_cat = new Category();
    $test_cat->setName('Events (All Campus and Public)');
    $test_cat->setActive(1);
    $manager->persist($test_cat);

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement = new Announcement();
    $announcement->setSubject('Make your own beeswax wraps!');
    $announcement->setAuthor('Amy Wahl');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_date);
    $announcement->setEndDate($announcement_date);
    $announcement->setText('Join Merry Lea for a hands-on workshop to create your very own beeswax cloth wrap. This handy addition to the kitchen provides an alternative to plastic wrap or single-use plastic bags. Select a cloth pattern of your choice and learn how to prepare the ingredients to make this sustainable tool. Participants will go home with one large and one small beeswax wrap.');
    $announcement->setApproval(1);
    $manager->persist($announcement);

    $test_cat = new Category();
    $test_cat->setName('Student Life');
    $test_cat->setActive(1);
    $manager->persist($test_cat);

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement = new Announcement();
    $announcement->setSubject('Transfer, Adult & Graduate Open House');
    $announcement->setAuthor('Paul Ritcher');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_date);
    $announcement->setEndDate($announcement_date);
    $announcement->setText('Transfer, Adult & Graduate Open House

    Join us for an open house tailored for individuals interested in learning more about:
    
    *Transferring into Goshen College’s traditional undergraduate program;
    *Our degree-completion and licensure programs catered toward working adults; or
    *Our top-notch graduate programs.');
    $announcement->setApproval(1);
    $manager->persist($announcement);

    $test_cat = new Category();
    $test_cat->setName('Buy, Sell, Trade, Lost & Found');
    $test_cat->setActive(1);
    $manager->persist($test_cat);

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement = new Announcement();
    $announcement->setSubject('St. Patrick\'s Day Potluck Celebration, Thurs., March 17th.');
    $announcement->setAuthor('Daniel Boling');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_date);
    $announcement->setEndDate($announcement_date);
    $announcement->setText('The Commuter Student Association invites you to come to our potluck lunch St. Patrick\'s Day Celebration on March 17th. from 11 am - 2 pm at the Leadership Hub (Union Building, next to Leaf Raker)');
    $announcement->setApproval(1);
    $manager->persist($announcement);

    $test_cat = new Category();
    $test_cat->setName('Convocation/Chapel');
    $test_cat->setActive(1);
    $manager->persist($test_cat);


    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement = new Announcement();
    $announcement->setSubject('This Week\'s Convo: Community Engagement Fair');
    $announcement->setAuthor('David Blanch');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_date);
    $announcement->setEndDate($announcement_date);
    $announcement->setText('This week for convo, we\'ll hear from some local Goshen residents, Shannan and Cory Martin, about how they invest in the local community. From 10:30 to 12:00, local agencies will be set up in the fellowship hall where you\'ll have a chance to connect around volunteer, internship, and work opportunities. Drinks, snacks, and door prizes from local organizations are part of the mix.');
    $announcement->setApproval(1);
    $manager->persist($announcement);

    $manager->flush();

  }

}

// EOF
