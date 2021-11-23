<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Kernel;
use App\Entity\Announcement;
use App\Entity\User;
use App\Entity\Category;
use App\Form\AnnouncementForm;
use Symfony\Component\String\Slugger\SluggerInterface;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class OverviewController extends AbstractController
{

  public function __construct()
  {
    
    $date = new \DateTime('now', new \DateTimeZone('America/Indiana/Indianapolis'));
    $this->date = $date->format('l, j F, Y');

  }


    /**
   * This should be the main page that everyone should see. Every user should be able to see this page and everything
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

    $announcement = $this->getDoctrine()
      // inits the database and table Announcements;
      ->getRepository(Announcement::class)
      ->find_today();

      return $this->render('overview.html.twig', [
        'date' => $this->date,
        'announcement' => $announcement,
      ]);

  }


  /**
   * Currently acts as the main input form for users.
   * Subject, Author (custom or autofilled), Category selection, text, and date
   * are available to be input.
   * 
   * @author Daniel Boling
   * @return rendered form and redirect to overview when submitted
   * 
   * @Route("/new", name="new_announcement")
   * @IsGranted("ROLE_USER")
   */
  public function new_announcement(Request $request, SluggerInterface $slugger): Response
  {

    $em = $this->getDoctrine()->getManager();
    $announcement = new Announcement();
    $user = $this->getUser();

    $info_form = $this->createForm(AnnouncementForm::class, $announcement);

    $info_form->handleRequest($request);

    if($info_form->isSubmitted() && $info_form->isValid()){
      $announcement = $info_form->getData();
      $file = $info_form->get('file')->getData();
      $announcement->setUser($user);
      $announcement->setApproval(0);
      // set approval to denied by default

      if ($file) {
        // if a file is in the form
        $original_filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safe_filename = $slugger->slug($original_filename);
        $new_filename = $safe_filename.'-'.uniqid().'.'.$file->guessExtension();

        try {
          $file->move(
            $this->getParameter('file_directory'),
            $new_filename,
          );
        } catch (FileException $e) {
          // handle exception
        }

        $announcement->setFilename($new_filename);

      }
      $em->persist($announcement);
      $em->flush();
      
      return $this->redirectToRoute('show_all');
    }

    return $this->render('new_announcement.html.twig', [
      'info_form' => $info_form->createView(),
      // 'date_form' => $date_form->createView(),
      'date' => $this->date,
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
   * @IsGranted("ROLE_USER")
   */
  public function show_user(): Response
  {

    $user = $this->getUser();

    $announcement = $user->getAnnouncements();
    
    return $this->render('user_overview.html.twig', [
      'date' => $this->date,
      'announcement' => $announcement,
    ]);

  }

  
  /**
   * The form page for adding new categories. This will be accessible only be admins.
   * 
   * @author Daniel Boling
   * @return rendered new_category.html.twig
   * 
   * @Route("/category/new", name="new_category")
   * @IsGranted("ROLE_ADMIN")
   */
  public function new_category(Request $request): Response
  {

    $em = $this->getDoctrine()->getManager();
    $category = new Category();
    // Init the category object for the category table;

    $form = $this->createFormBuilder($category)
      ->add('name', TextType::class)
      ->add('submit', SubmitType::class, ['label' => 'Add Category'])
      ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
      // should pull data from the form and flush it to the database;
      $category = $form->getData();   
      $category->setActive(1);
      // will always be set to active by default;
      $em->persist($category);
      $em->flush();
      
      return $this->redirectToRoute('show_all');
      // this will be changed to redirect to the show_categories page in the next update;
    }

    return $this->render('new_category.html.twig', [
      'form' => $form->createView(),
      'date' => $this->date,
    ]);
  }

  /**
   * The page for listing all categories in the system, wether they are active or not,
   * and then providing simple means of toggling active/inactive.
   * Will only accessible by admins
   * 
   * @author Daniel Boling
   * 
   * @Route("/category/list", name="list_category")
   * @IsGranted("ROLE_ADMIN")
   */
  public function list_category(Request $request): Response
  {

    $categories = $this->getDoctrine()
    // inits the database and Category table;
    ->getRepository(Category::class)
    ->findAll();


    return $this->render('list_category.html.twig', [
      'categories' => $categories,
      'date' => $this->date,
    ]);
  }
  
  /**
   * Is called on button-click from twig file, updates active categories, and redirects to list_category
   * 
   * @author Daniel Boling
   * @return redirect to list_category
   * 
   * @Route("/category/list/{id}", name="update_category")
   * @IsGranted("ROLE_ADMIN")
   */
  public function update_category(Request $request, $id): Response
  {

    $em = $this->getDoctrine()->getManager();

    $category = $this->getDoctrine()
      ->getRepository(Category::class)
      ->find($id);
      
    if ($category->getActive() == 1)
      {
        $category->setActive(0);

      } else {
        $category->setActive(1);
      }
      $em->persist($category);
      $em->flush();
      

    return $this->redirectToRoute('list_category');

  }

  /**
   * This should be the main page that everyone should see. Every user should be able to see this page and everything
   * on it. This will be modified more clearly from it's current state. Currently
   * being used as a testing stage for database outputs.
   * 
   * @author Daniel Boling
   * @return rendered moderation_announcements.html.twig
   * 
   * @Route("/moderation/announcements", name="moderation_announcements")
   * @IsGranted("ROLE_MODERATOR")
   */
  public function moderation_announcements(): Response
  {

    $announcement = $this->getDoctrine()
      // inits the database and table Announcements;
      ->getRepository(Announcement::class)
      ->find_today('now', 0);

      return $this->render('moderation_announcements.html.twig', [
        'date' => $this->date,
        'announcement' => $announcement,
      ]);

  }

  /**
   * Is called on button-click from twig file, updates announcement approval, and redirects to list_category
   * 
   * @author Daniel Boling
   * @return redirect to list_category
   * 
   * @Route("/moderation/announcement/{id}", name="toggle_announcement_approval")
   * @IsGranted("ROLE_MODERATOR")
   */
  public function toggle_announcement_approval(Request $request, $id): Response
  {

    $em = $this->getDoctrine()->getManager();

    $announcement = $this->getDoctrine()
      ->getRepository(Announcement::class)
      ->find($id)
    ;
      
    if ($announcement->getApproval() == 0)
    // if the announcement is denied, set it to approved.
      {
        $announcement->setApproval(1);

      } else {
      // if the condition gets here, the announcement is already approved, so set it to denied.
        $announcement->setApproval(0);
      }
      $em->persist($announcement);
      $em->flush();
      

    return $this->redirectToRoute('moderation_announcements');

  }

  /**
   * Is called on button-click from twig file, sends user to the edit form page.
   * 
   * @author Daniel Boling
   * @return redirect to edit_announcement
   * 
   * @Route("/modify/announcement/{id}", name="modify_announcement")
   * @IsGranted("ROLE_USER")
   */
  public function modify_announcement(Request $request, $id): Response
  {

    $em = $this->getDoctrine()->getManager();

    $announcement = $this->getDoctrine()
      ->getRepository(Announcement::class)
      ->find($id)
    ;
    if($this->getUser() == $announcement->getUser() or $this->isGranted('ROLE_MODERATOR')){

      $info_form = $this->createForm(AnnouncementForm::class, $announcement);

      $info_form->handleRequest($request);

      if($info_form->isSubmitted() && $info_form->isValid()){
        $announcement = $info_form->getData();
        $announcement->setApproval(0);
        // set approval to denied by default
        $em->persist($announcement);
        $em->flush();
        
        if ($this->getUser() == $announcement->getUser()){
          return $this->redirectToRoute('show_all_user');
        } else {
          return $this->redirectToRoute('moderation_announcements');
        }

      }

      return $this->render('modify_announcement.html.twig', [
        'info_form' => $info_form->createView(),
        // 'date_form' => $date_form->createView(),
        'date' => $this->date,
      ]);
    } else {
      return throw new AccessDeniedHttpException("Unauthorized");
    }

  }


  /**
   * Function for handling file rendering
   * 
   * @author Daniel Boling
   * @return Rendered file
   * 
   * @Route(name="view_file")
   */
  public function view_file(Request $request, $url): Response
  {

    return $this->render($url);

  }

}

// EOF
