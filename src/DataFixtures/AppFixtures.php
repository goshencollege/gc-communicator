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
    $test_user->setUsername('fixture_user');
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
    $announcement_date = new \DateTime('now', new \DateTimeZone('GMT'));
    $announcement = new Announcement();
    $announcement->setSubject('fixture_subject');
    $announcement->setAuthor('fixture_author');
    $announcement->setCategory($test_cat);
    $announcement->setUser($test_user);
    $announcement->setStartDate($announcement_date);
    $announcement->setEndDate($announcement_date);
    $announcement->setText('fixture_text');
    $announcement->setApproval(1);
    $manager->persist($announcement);

    $manager->flush();

  }

}

// EOF
