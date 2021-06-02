<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Articles;

class OverviewController extends AbstractController
{

  /**
   * @Route("/add", name="add_info")
   */
  public function add_info(): Response
  {

    $entityManager = $this->getDoctrine()->getManager();

    $article = new Articles();
    $articleId = strval($article->getId());
    $article->setSubject('testSubject'.$articleId);
    $article->setAuthor('testAuthor'.$articleId);
    $article->setText('testText'.$articleId);

    $entityManager->persist($article);

    $entityManager->flush();

    return new Response('Created new article with id '.$article->getId());
  }

  /**
  * @Route("/overview", name="show_all")
  */
  public function show_all(): Response
  {

    $date = getdate();

    $articles = $this->getDoctrine()
      ->getRepository(Articles::class)
      ->findAll();
    
      if(!$articles){
        throw $this->createNotFoundException(
          'No articles found!'
        );
      }

      return $this->render('current.html.twig', [
        'date' => $date,
        'articles' => $article,
      ]);

  }


}

// EOF
