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
   * @ORM\Column(type="text")
   */
  private $text;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="announcements")
   */
  private $category;

  /**
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $recurrence;

  /**
   * @ORM\Column(type="date")
   */
  private $end_date;

  /**
   * @ORM\Column(type="date")
   */
  private $start_date;

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

  public function getCategory(): ?Category
  {
      return $this->category;
  }

  public function setCategory(?Category $category): self
  {
      $this->category = $category;

      return $this;
  }

  public function getRecurrence(): ?string
  {
      return $this->recurrence;
  }

  public function setRecurrence(?string $recurrence): self
  {
      $this->recurrence = $recurrence;

      return $this;
  }

  public function getEndDate(): ?\DateTimeInterface
  {
      return $this->end_date;
  }

  public function setEndDate(\DateTimeInterface $end_date): self
  {
      $this->end_date = $end_date;

      return $this;
  }

  public function getStartDate(): ?\DateTimeInterface
  {
      return $this->start_date;
  }

  public function setStartDate(\DateTimeInterface $start_date): self
  {
      $this->start_date = $start_date;

      return $this;
  }

}
