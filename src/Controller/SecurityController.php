<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
  /**
   * @Route("/login", name="app_login")
   */
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    // if ($this->getUser()) {
    //     return $this->redirectToRoute('target_path');
    // }

    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
  }


  /**
   * @Route("/logout", name="app_logout")
   */
  public function logout()
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }


  // /**
  //  * A simple function dedicated for redirection when the incorrect user is signed in,
  //  * or there is no user signed it at all.
  //  * 
  //  * @author Daniel Boling
  //  * @return rendered unauthenticated.html.twig
  //  * 
  //  * @Route('/unauthorized', name='unauthorized')
  //  */
  // public function unauthorized(): Response
  // {
  //   return $this->render('unauthorized.html.twig', [
  //     'date' => $this->date,
  //   ]);
  // }
  
}

// EOF
