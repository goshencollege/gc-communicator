<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Announcement;
use App\Entity\UserRepository;


class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {

    $announcement = new Announcement();
    $announcement_date = new \DateTime('2021-07-14');
    $announcement->setSubject('autoSubject');
    $announcement->setAuthor('autoAuthor');
    $announcement->setUser($this->getReference('user_1'));
    $announcement->setDate($announcement_date);
    $announcement->setText('autoText');

  }
}

// EOF
