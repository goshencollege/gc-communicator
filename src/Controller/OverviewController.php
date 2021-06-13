<?php

namespace App\Controller;

use App\Entity\Articles;
use App\Entity\Article;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class OverviewController extends AbstractController
{

  /**
   * @author Daniel Boling
   * @return rendered HTML form
   * 
   * @Route("/form", name="add_article_form")
   */
  public function add_article_form(Request $Request): Response
  {

    $article = new Article();

    $form = $this->createFormBuilder($article)
      ->add('subject', TextType::class)
      ->add('author', TextType::class)
      ->add('text', TextType::class)
      ->getForm();
        
    return $this->render('add_article.html.twig', [
      'form' => $form->createView()
    ]);

  }

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
   * @Route("/add", name="add_article")
   */
  public function add_article(): Response
  {

    $entityManager = $this->getDoctrine()->getManager(); // Simply understanding this as a basic "rule" of symfony;



    /**
    $article = new Articles();    // Init the articles object for the Articles table. Calls found in /src/Entity/Articles.php;
    $article->setSubject('testSubject');
    $article->setAuthor('testAuthor');
    $article->setText('testText');

    $entityManager->persist($article);

    $entityManager->flush();
    */

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
