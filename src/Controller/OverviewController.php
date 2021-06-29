<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Kernel;
use App\Entity\Announcement;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

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
   */
  public function add_info(Request $request): Response
  {

    $date = getDate();
    $entityManager = $this->getDoctrine()->getManager(); // Simply understanding this as a basic "rule" of symfony;
    
    $announcement = new Announcement();    // Init the articles object for the Articles table. Calls found in /src/Entity/Articles.php;

    $form = $this->createFormBuilder($announcement)       // Move this form creation to its own class eventually;
      ->add('subject', TextType::class)
      ->add('author', TextType::class)
      ->add('text', TextType::class)
      ->add('submit', SubmitType::class, ['label' => 'Submit Announcement'])
      ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){

      $announcement = $form->getData();   // should pull data from the form and flush it to the database;
      $entityManager->persist($announcement);
      $entityManager->flush();

    }

    return $this->render('add.html.twig', [
      'form' => $form->createView(),
      'date' => $date,
    ]);

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

    $announcement = $this->getDoctrine()
      ->getRepository(Announcement::class)    // inits the database and table Articles;
      ->findAll();    // defined in /src/Entity/Articles.php;

      return $this->render('overview.html.twig', [
        'date' => $date,
        'announcement' => $announcement,
      ]);

  }

}

// EOF
