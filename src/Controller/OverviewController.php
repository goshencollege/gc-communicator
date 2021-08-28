<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Kernel;
use App\Entity\Announcement;
use App\Entity\User;
use App\Entity\Category;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class OverviewController extends AbstractController
{

  public function __construct()
  {
    $date = new \DateTime;
    $this->date = $date->format('l, j F, Y');
  }

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

    $em = $this->getDoctrine()->getManager();
    $announcement = new Announcement();
    // Init the announcements object for the Announcement table;
    $user = $this->getUser();

    $form = $this->createFormBuilder($announcement)
      ->add('subject', TextType::class)
      ->add('author', TextType::class)
      ->add('category', EntityType::class, [
        'class' => Category::class,
        'query_builder' => function(EntityRepository $er)
        {
          return $er->createQueryBuilder('a')
            ->andWhere('a.active = :val')
            ->setParameter('val', 1)
            ->orderBy('a.name', 'ASC')
          ;
        },
        'choice_label' => 'name',
        'placeholder' => 'Category',
      ])
      ->add('text', TextareaType::class)
      ->add('date', DateType::class, [
        'data' => new \DateTime,
      ])
      ->add('submit', SubmitType::class, ['label' => 'Submit Announcement'])
      ->getForm();

    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
      $announcement = $form->getData();   
      $announcement->setUser($user);
      $em->persist($announcement);
      $em->flush();
      
      return $this->redirectToRoute('show_all');
    }

    return $this->render('add.html.twig', [
      'form' => $form->createView(),
      'date' => $this->date,
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
    
    return $this->render('overview.html.twig', [
      'date' => $this->date,
      'announcement' => $announcement,
    ]);

  }

  /**
   * The form page for adding new categories. This will be accessible only be admins.
   * 
   * @author Daniel Boling
   * @return rendered add-category.html.twig
   * 
   * @Route("/category/add", name="add_category")
   * @IsGranted("ROLE_USER")
   */
  public function add_category(Request $request): Response
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

    return $this->render('add-category.html.twig', [
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
   * @IsGranted("ROLE_USER")
   */
  public function list_category(Request $request): Response
  {

    $categories = $this->getDoctrine()
    // inits the database and Category table;
    ->getRepository(Category::class)
    ->findAll();


    return $this->render('list-category.html.twig', [
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

}

// EOF
