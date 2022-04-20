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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Kernel;
use App\Repository\AnnouncementRepository;
use App\Repository\CategoryRepository;
use App\Entity\Announcement;
use App\Entity\User;
use App\Entity\Category;
use App\Form\AnnNewForm;
use App\Form\AnnModifyForm;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class AnnouncementController extends AbstractController
{

  private $UploaderHelper;

  public function __construct(EntityManagerInterface $entityManager, AnnouncementRepository $announcement_repo, CategoryRepository $category_repo)
  {
    $this->em = $entityManager;
    
    $date = new \DateTime('now', new \DateTimeZone('America/Indiana/Indianapolis'));
    $this->date = $date->format('l, j F, Y');

    $this->announcement_repo = $announcement_repo;
    $this->category_repo = $category_repo;

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
    $announcement = new Announcement();
    $user = $this->getUser();

    $ann_form = $this->createForm(AnnNewForm::class, $announcement);

    $ann_form->handleRequest($request);

    if($ann_form->isSubmitted() && $ann_form->isValid()) 
    {
      $announcement = $ann_form->getData();
      $announcement->setUser($user);
      $announcement->setApproval(0);
      // set approval to denied by default
      $this->em->persist($announcement);
      $this->em->flush();
      
      return $this->redirectToRoute('show_all');
    }

    return $this->render('new_announcement.html.twig', [
      'ann_form' => $ann_form->createView(),
      'date' => $this->date,
    ]);

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
    $announcements = $this->announcement_repo->find_today('now', 0);

    $categories = $this->category_repo->findAll();

    return $this->render('moderation_announcements.html.twig', [
      'date' => $this->date,
      'announcements' => $announcements,
      'categories' => $categories,
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
    $announcement = $this->announcement_repo->find($id);
      
    if ($announcement->getApproval() == 0)
    // if the announcement is denied, set it to approved.
    {
      $announcement->setApproval(1);

    } else {
    // if the condition gets here, the announcement is already approved, so set it to denied.
      $announcement->setApproval(0);
    }
    $this->em->persist($announcement);
    $this->em->flush();
      
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
    $announcement = $this->announcement_repo->find($id);
    if($this->getUser() == $announcement->getUser() or $this->isGranted('ROLE_MODERATOR'))
    // security to only allow owners and moderators to modify an announcement
    {
      $ann_form = $this->createForm(AnnModifyForm::class, $announcement);
      $ann_form->handleRequest($request);

      if($ann_form->isSubmitted() && $ann_form->isValid())
      {
        $announcement = $ann_form->getData();
        $announcement->setApproval(0);
        // set approval to denied by default
        $this->em->persist($announcement);
        $this->em->flush();
        
        if ($this->getUser() == $announcement->getUser())
        {
          return $this->redirectToRoute('show_all_user');
        } else {
          return $this->redirectToRoute('moderation_announcements');
        }

      }

      return $this->render('modify_announcement.html.twig', [
        'ann_form' => $ann_form->createView(),
        'date' => $this->date,
      ]);
    } else {
      throw new AccessDeniedHttpException("Unauthorized");
    }

  }

  /**
   * Function to copy an announcement exactly, load the modification page by default.
   * 
   * @author Daniel Boling
   * 
   * @Route("/copy/announcement/{id}", name="copy_announcement")
   */
  public function copy_announcement(Request $request, $id): Response
  {
    $new_announcement = new Announcement();

    $announcement = $this->announcement_repo->find($id);

    if($this->getUser() == $announcement->getUser())
    {
      $ann_form = $this->createForm(AnnNewForm::class, clone $announcement);
      $ann_form->handleRequest($request);

      if($ann_form->isSubmitted() && $ann_form->isValid())
      {
        $new_announcement = $ann_form->getData();
        $new_announcement->setApproval(0);
        $em->persist($new_announcement);
        $em->flush();

        return $this->redirectToRoute('show_all_user');
      }

      return $this->render('modify_announcement.html.twig', [
        'ann_form' => $ann_form->createView(),
        'date' => $this->date,
      ]);

    } else {
      throw new AccessDeniedHttpException("Unauthorized");
    }

  }

}

// EOF
