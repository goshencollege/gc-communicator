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
use App\Entity\Announcement;
use App\Entity\User;
use App\Entity\Category;
use App\Form\AnnouncementForm;
use App\Repository\AnnouncementRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OverviewController extends AbstractController
{

  private $UploaderHelper;

  public function __construct(EntityManagerInterface $entityManager, AnnouncementRepository $announcement_repo, CategoryRepository $category_repo)
  {
    $this->em = $entityManager;
    $date = new \DateTime('now', new \DateTimeZone('GMT'));
    $this->date = $date->format('l, j F, Y');

    $this->announcement_repo = $announcement_repo;
    $this->category_repo = $category_repo;

  }


    /**
   * This should be the main page that everyone should see. Every user should be able to see this page and everything
   * on it. This will be modified more clearly from it's current state. Currently
   * being used as a testing stage for database outputs.
   * 
   * @author Daniel Boling
   * @return rendered overview.html.twig
   * 
   * @Route("/", name="show_all")
   */
  public function show_all(): Response
  {
    $announcements = $this->announcement_repo->find_today();
    $categories = $this->category_repo->findAll();

      return $this->render('overview.html.twig', [
        'date' => $this->date,
        'announcements' => $announcements,
        'categories' => $categories,
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
   * Mailing function - This will take all approved announcements for the day and compile them into
   * email that is sent to a specific group (?) for the college.
   * 
   * @author Daniel Boling
   * 
   * @Route("/email", name="send_email")
   * @IsGranted("ROLE_MODERATOR")
   */
  public function send_email(MailerInterface $mailer, Request $request): Response
  {
    $submittedToken = $request->get('csrf_token');

    if ($this->isCsrfTokenValid('send email', $submittedToken))
    {
      // prevent users from entering the route url to send an email
      $announcements = $this->announcement_repo->find_today();
      $categories = $this->category_repo->findAll();

      $email = (new TemplatedEmail())
        ->to("someone@example.com")
        // leave above as is, the emails are handled in the .env.local file, keeping security during repo update.
        ->subject('Communicator - ' . $this->date)
        ->htmlTemplate('mailer.html.twig')
        //->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply')
        ->context([
          'date' => $this->date,
          'announcements' => $announcements,
          'categories' => $categories,
        ])
      ;
      $mailer->send($email);    

      return $this->redirectToRoute('moderation_announcements');

    } else {
      return $this->redirectToRoute('moderation_announcements');

    }

  }


}


// EOF
