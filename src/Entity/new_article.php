<?php

namespace App\Entity;

/**
 * Some basic class for the form management - example pulled from
 * https://symfony.com/doc/current/forms.html
 * 
 * @author Daniel Boling
 */

class Article
{
  protected $subject;
  protected $author;
  protected $text;

  /**
   * Should set the subject of the article
   * 
   * @author Daniel Boling
   */
  public function setSubject(string $subject): string
  {
    $this->subject = $subject;
  }

  /**
   * Should set the author of the article
   * This will probably stay the same even after SAML auth
   * 
   * @author Daniel Boling
   */
  public function setAuthor(string $author): string
  {
    $this->author = $author;
  }

  /**
   * Should set the text for the article
   * 
   * @author Daniel Boling
   */
  public function setText(string $text): string
  {
    $this->text = $text;
  }

}

// EOF