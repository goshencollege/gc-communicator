<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
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
    $this->addReference('user_1', $user);
    $user->setPassword($this->passwordHasher->hashPassword(
        $user,
        '12345'
    ));
    $manager->persist($user);

    $user = new User();
    $user->setUsername('dboling');
    $this->addReference('user_2', $user);
    $user->setPassword($this->passwordHasher->hashPassword(
      $user,
      '12345'
    ));
    $manager->persist($user);

    $manager->flush();
  }
}

// EOF
