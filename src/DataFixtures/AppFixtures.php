<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Announcement;
use App\DataFixtures\UserFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {

    $announcement = new Announcement();
    $announcement_date = new \DateTime('2021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');

  }

  public function getDependencies()
  {
    return [
      UserFixtures::class,
    ];
  }

}

// EOF
