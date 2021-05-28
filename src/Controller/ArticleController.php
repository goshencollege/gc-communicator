<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Article;

class ArticleController extends AbstractController
{
  /**
   * @Route("/a", name="article")
   */
  public function index(): Response
  {
    
    $entityManager = $this->getDoctrine()->getManager();

    $article = new Article();
    $article->setSubject('SubjectTest');
    $article->setAuthor('AuthorTest');
    $article->setText('textTest');

    $entityManager->persist($article);

    $entityManager->flush();

    return new Response('Saved new article with id '.$article->getId());

  }

  /**
   * @Route("/a/{id}", name="show")
   */
  public function show(int $id): Response
  {
    $article = $this->getDoctrine()
      ->getRepository(Article::class)
      ->find($id);
    
      if(!$article){
        throw $this->createNotFoundException(
          'No article found for id '.$id
        );
      }

      return new Response('Check out this article: '.$article->getSubject());

  }

}

// EOF
