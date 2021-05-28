<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Article;

class MainController extends AbstractController
{
  
  /**
   *  @Route("/current")
   */
  public function current(): Response
  {

    $announce = array("count" => 7);


    return $this->render('current.html.twig', [
      'announce' => $announce,
      'date' => $date,
    ]);

  }

  /**
   * @Route("/article", name="article")
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
   * @Route("/article/all", name="show_all")
   */
  public function show_all(): Response
  {

    $date = getdate();

    $article = $this->getDoctrine()
      ->getRepository(Article::class)
      ->findAll();
    
      if(!$article){
        throw $this->createNotFoundException(
          'No articles found!'
        );
      }
      
      //var_dump($article);

      return $this->render('current.html.twig', [
        'date' => $date,
        'article' => $article,
      ]);

  }

}

// EOF
