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
    $test_user->setUsername('fixture_user');
    $test_user->setRoles(['ROLE_USER']);
    $test_user->setPassword($this->passwordHasher->hashPassword(
        $test_user,
        '12345'
    ));
    $manager->persist($test_user);

    $test_user = new User();
    $test_user->setUsername('test_user');
    $test_user->setRoles(['ROLE_USER']);
    $test_user->setPassword($this->passwordHasher->hashPassword(
        $test_user,
        '12345'
    ));
    $manager->persist($test_user);

    $test_cat = new Category();
    $test_cat->setName('fixture_category');
    $test_cat->setActive(1);
    $manager->persist($test_cat);

    // loading fixtures for category table
    $cat = new Category();
    $cat->setName('Rooms');
    $cat->setActive(1);
    $manager->persist($cat);

    $cat = new Category();
    $cat->setName('Buildings');
    $cat->setActive(1);
    $manager->persist($cat);

    $cat = new Category();
    $cat->setName('Classes');
    $cat->setActive(0);
    $manager->persist($cat);

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement = new Announcement();
    $announcement_start_date = new \DateTime('yesterday');
    $announcement_end_date = new \DateTime('yesterday');
    $announcement->setSubject('fixture_subject');
    $announcement->setAuthor('fixture_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_start_date);
    $announcement->setEndDate($announcement_end_date);
    $announcement->setText('fixture_text');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_start_date = new \DateTime('now');
    $announcement_end_date = new \DateTime('now');
    $announcement->setSubject('fixture_subject');
    $announcement->setAuthor('fixture_author');
    $announcement->setCategory($cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_start_date);
    $announcement->setEndDate($announcement_end_date);
    $announcement->setText('fixture_text');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_start_date = new \DateTime('tomorrow');
    $announcement_end_date = new \DateTime('tomorrow');
    $announcement->setSubject('fixture_subject');
    $announcement->setAuthor('fixture_author');
    $announcement->setCategory($cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_start_date);
    $announcement->setEndDate($announcement_end_date);
    $announcement->setText('fixture_text');
    $manager->persist($announcement);

    // announcement set to recurr through all three of the above
    $announcement = new Announcement();
    $announcement_start_date = new \DateTime('yesterday');
    $announcement_end_date = new \DateTime('tomorrow');
    $announcement->setStartDate($announcement_start_date);
    $announcement->setEndDate($announcement_end_date);
    $rule = (new \Recurr\Rule)
      ->setStartDate($announcement_start_date)
      // get in the habit of formatting like this
      ->setTimezone('America/New_York')
      ->setFreq('DAILY')
      ->setByDay(['MO', 'TU'])
      ->setUntil($announcement_end_date)
    ;
    $announcement->setRecurrence($rule->getString());
    $announcement->setSubject('fixture_subject');
    $announcement->setAuthor('fixture_author');
    $announcement->setCategory($cat);
    $announcement->setUser($test_user);
    $announcement->setText('fixture_text');
    $manager->persist($announcement);

    $manager->flush();

  }

}

// EOF
