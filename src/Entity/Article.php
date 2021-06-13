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
  protected $date_start;
  protected $date_end;

  /**
   * Should get the subject of the article - I assume from the HTML form
   * This is REQUIRED. There MUST be a get to each set or the form will return an error.
   * 
   * @author Daniel Boling
   */
  public function getSubject(): string
  {
    return $this->subject;
  }

  /**
   * Should set the subject of the article
   * 
   * @author Daniel Boling
   */
  public function setSubject(string $subject): void
  {
    $this->subject = $subject;
  }

  /**
   * Should get the author of the article
   * 
   * @author Daniel Boling
   */
  public function getAuthor(): string
  {
    return $this->author;
  }

  /**
   * Should set the author of the article
   * This will probably stay the same even after SAML auth
   * 
   * @author Daniel Boling
   */
  public function setAuthor(string $author): void
  {
    $this->author = $author;
  }

  /**
   * Should get the text for the article
   * 
   * @author Daniel Boling
   */
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * Should set the text for the article
   * 
   * @author Daniel Boling
   */
  public function setText(string $text): void
  {
    $this->text = $text;
  }

  /**
   * Should get the start date for the article
   * An algorithm will be put in place later to monitor
   * running date
   * 
   * @author Daniel Boling
   */
  public function getDateStart(): ?\DateTime
  {
    return $this->dateStart;
  }

    /**
   * Should set the start date for the article
   * An algorithm will be put in place later to monitor
   * running date
   * 
   * @author Daniel Boling
   */
  public function setDateStart(?\DateTime $dateStart): void
  {
    $this->dateStart = $dateStart;
  }

    /**
   * Should get the end date for the article
   * An algorithm will be put in place later to monitor
   * running date
   * 
   * @author Daniel Boling
   */
  public function getDateEnd(): ?\DateTime
  {
    return $this->dateEnd;
  }

    /**
   * Should set the end date for the article
   * An algorithm will be put in place later to monitor
   * running date
   * 
   * @author Daniel Boling
   */
  public function setDateEnd(?\DateTime $dateEnd): void
  {
    $this->dateEnd = $dateEnd;
  }

}

// EOF
