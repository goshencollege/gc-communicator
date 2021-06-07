<?php
use PHPUnit\Framework\TestCase;
use App\Entity\Articles;

final class ArticlesTest extends TestCase 
{
    /**
     * Test creating a new Article then verify it's info.
     * 
     * @author David King
     * 
     */
    public function testCreate(): void
    {
        $article = new Articles();
        $article->setSubject('testSubject');
        $article->setAuthor('testAuthor');
        $article->setText('testText');

        $this->assertSame('testSubject', $article->getSubject());
        $this->assertSame('testAuthor', $article->getAuthor());
        $this->assertSame('testText', $article->getText());
   }
}

// EOF