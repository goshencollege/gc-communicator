<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
  private $passwordHasher;
  public const ADMIN_USER_REFERENCE = 'admin-user';

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
    /**
    $user = new User();
    $user->setUsername('dboling');
    $user->setPassword($this->passwordHasher->hashPassword(
      $user,
      '12345'
    ));
    $manager->persist($user);
    */
    $manager->flush();
    $this->addReference(self::ADMIN_USER_REFERENCE, $user);

  }
}

// EOF
