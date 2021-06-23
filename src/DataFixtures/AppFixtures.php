<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Article;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {
    $article = new Articles();    
    $article->setSubject('testSubject');
    $article->setAuthor('testAuthor');
    $article->setText('testText');
    $manager->persist($article);

    $manager->flush();
  }
}

// EOF
