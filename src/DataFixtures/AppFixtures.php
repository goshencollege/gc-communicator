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
    $user->setPassword($this->passwordHasher->hashPassword(
        $user,
        '12345'
    ));
    $manager->persist($user);

    $user = new User();
    $user->setUsername('dboling');
    $user->setPassword($this->passwordHasher->hashPassword(
        $user,
        '12345'
    ));
    $manager->persist($user);

    $testUser = new User();
    $testUser->setUsername('testUser');
    $testUser->setPassword($this->passwordHasher->hashPassword(
        $testUser,
        '12345'
    ));
    $manager->persist($testUser);

    // one announcement set to a past date, one to the current date (constant) and one for a future date
    $announcement = new Announcement();
    $announcement_date = new \DateTime('2021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory('testCategory');
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_date = new \DateTime('now');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory('testCategory');
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);

    $announcement = new Announcement();
    $announcement_date = new \DateTime('3021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setCategory('testCategory');
    $announcement->setUser($testUser);
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');
    $manager->persist($announcement);


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

    $manager->flush();

  }

}

// EOF
