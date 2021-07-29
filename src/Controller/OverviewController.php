<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Kernel;
use App\Entity\Announcement;
use App\Entity\User;

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
   * @return rendered form and redirect to overview when submitted
   * 
   * @Route("/add", name="add_info")
   * @IsGranted("ROLE_USER")
   */
  public function add_info(Request $request): Response
  {

    $date = getDate();
    // Simply understanding this as a basic "rule" of symfony;
    $entityManager = $this->getDoctrine()->getManager(); 
    
    // Init the articles object for the Articles table. Calls found in /src/Entity/Articles.php;
    $announcement = new Announcement();    
    $user = $this->getUser();

    // Move this form creation to its own class eventually;
    $form = $this->createFormBuilder($announcement)       
      ->add('subject', TextType::class)
      ->add('author', TextType::class)
      ->add('text', TextType::class)
      ->add('date', DateType::class)
      ->add('submit', SubmitType::class, ['label' => 'Submit Announcement'])
      ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){

      // should pull data from the form and flush it to the database;
      $announcement = $form->getData();   
      $announcement->setUser($user);
      $entityManager->persist($announcement);
      $entityManager->flush();
      
      return $this->redirectToRoute('show_all');
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
      // inits the database and table Articles;
      ->getRepository(Announcement::class)
      ->findToday();

      return $this->render('overview.html.twig', [
        'date' => $date,
        'announcement' => $announcement,
      ]);

  }

  /**
   * Basically the same page as /overview, except shows all announcements of
   * the currently logged in user.
   * Future Additions - allow users to modify announcements
   * 
   * @author Daniel Boling
   * @return rendered overview.html.twig
   * 
   * @Route("/overview/user", name="show_all_user")
   */
  public function show_user(): Response
  {

    $date = getdate();
    $user = $this->getUser();

    $announcement = $user->getAnnouncements();
    
    return $this->render('overview.html.twig', [
      'date' => $date,
      'announcement' => $announcement,
    ]);

  }

}

// EOF
