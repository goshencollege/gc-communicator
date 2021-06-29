<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Kernel;
use App\Entity\Articles;

class OverviewController extends AbstractController
{

  /**
   * Simply adds a row into the database through a standard function
   * Will eventually have an input form for users to input things like
   * subject and text, their name and email will probably be pulled
   * automatically through the authenticator (assuming I have access to
   * that in this file)
   * 
   * @author Daniel Boling
   * @return string and id of new test article
   * 
   * @Route("/add", name="add_info")
   * @IsGranted("ROLE_USER")
   */
  public function add_info(): Response
  {

    $entityManager = $this->getDoctrine()->getManager(); // SiAAAmply understanding this as a basic "rule" of symfony;

    $article = new Articles();    // Init the articles object for the Articles table. Calls found in /src/Entity/Articles.php;
    $articleId = strval($article->getId());
    $article->setSubject('testSubject'.$articleId);
    $article->setAuthor('testAuthor'.$articleId);
    $article->setText('testText'.$articleId);

    $entityManager->persist($article);

    $entityManager->flush();

    return new Response('Created new article with id '.$article->getId());
  }

  /**
   * This should be the main page that everyone should see. Ever user (not sure
   * if we're doing guest users or not) should be able to see this page and everything
   * on it. This will be modified more clearly from it's current state. Currently
   * being used as a testing stage for database outputs.
   * 
   * @author Daniel Boling
   * @return rendered overview.html.twig
   * 
   * @Route("/overview", name="show_all")
   */
  public function show_all(): Response
  {

    $date = getdate();

    $articles = $this->getDoctrine()
      ->getRepository(Articles::class)    // inits the database and table Articles;
      ->findAll();    // defined in /src/Entity/Articles.php;
    
      if($articles == null){   // testing for blank database or values;
        throw $this->createNotFoundException(   // I don't like this resolution very much, but it works for now;
          'No articles found!'
        );
      }

      return $this->render('overview.html.twig', [
        'date' => $date,
        'articles' => $articles,
      ]);

  }

}

// EOF
