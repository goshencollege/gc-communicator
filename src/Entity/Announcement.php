<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnnouncementRepository::class)
 */
class Announcement
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $Subject;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $Author;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="announcements")
   */
  private $User;

  /**
   * @ORM\Column(type="date")
   */
  private $Date;

  /**
   * @ORM\Column(type="text")
   */
  private $text;

  /**
   * @ORM\Column(type="string", length=255)
   */
  private $category;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getSubject(): ?string
  {
    return $this->Subject;
  }

  public function setSubject(string $Subject): self
  {
    $this->Subject = $Subject;

    return $this;
  }

  public function getAuthor(): ?string
  {
    return $this->Author;
  }

  public function setAuthor(string $Author): self
  {
    $this->Author = $Author;

    return $this;
  }


  public function getDate(): ?\DateTimeInterface
  {
    return $this->Date;
  }

  public function setDate(\DateTimeInterface $Date): self
  {
    $this->Date = $Date;

    return $this;
  }

  public function getText(): ?string
  {
    return $this->text;
  }

  public function setText(string $text): self
  {
    $this->text = $text;

    return $this;
  }

  public function getUser(): ?User
  {
      return $this->User;
  }

  public function setUser(?User $User): self
  {
      $this->User = $User;

      return $this;
  }

  public function getCategory(): ?string
  {
      return $this->category;
  }

  public function setCategory(string $category): self
  {
      $this->category = $category;

      return $this;
  }
}
