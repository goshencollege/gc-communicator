<?php
namespace App\Tests\Repository;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticlesRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Setup the test environment.  This is called automatically by PHPUnit
     * 
     * @author David King
     * 
     */
    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Create a new Article and persist it to the database.  Then retreive
     * all of the articles from the database and make sure the values match
     * the article that was just persisted.
     * 
     * @todo check the number of articles retreived from the database
     * 
     * @author David King
     * 
     */
    public function testCreate(): void
    {
        $existing_articles = count(
            $this->entityManager
            ->getRepository(Articles::class)
            ->findAll());

        $article = new Articles();    
        $article->setSubject('testSubject');
        $article->setAuthor('testAuthor');
        $article->setText('testText');
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        $article2 = $this->entityManager
            ->getRepository(Articles::class)
            ->findAll();

        $this->assertNotNull($article2);
        $this->assertSame(++$existing_articles, count($article2));
        $this->assertSame($article->getSubject(), $article2[0]->getSubject());
        $this->assertSame($article->getAuthor(), $article2[0]->getAuthor());
        $this->assertSame($article->getText(), $article2[0]->getText());
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
        $this->entityManager->close();
        $this->entityManager = null;
    }
}

//EOF
