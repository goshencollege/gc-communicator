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
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Asset\UrlPackage;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class CategoryController extends AbstractController
{

  private $UploaderHelper;

  public function __construct()
  {
    
    $date = new \DateTime('now', new \DateTimeZone('America/Indiana/Indianapolis'));
    $this->date = $date->format('l, j F, Y');

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
      
      return $this->redirectToRoute('list_category');
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
   * @Route("/category/update/{id}", name="update_category")
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

}


// EOF
