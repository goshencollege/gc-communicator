<?php

namespace App\Entity;

use App\Repository\AnnouncementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnnouncementRepository::class)
 * @Vich\Uploadable
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
   * @ORM\Column(type="date")
   */
  private $start_date;

  /**
   * @ORM\Column(type="date", nullable=true)
   */
  private $end_date;


  private $continue_date;

  /**
   * @ORM\Column(type="integer")
   */
  private $approval;
  
  /**
  * @ORM\Column(type="string", nullable=true)
  */
  private $filename;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   *
   * @var \DateTimeInterface|null
   */
  private $updatedAt;

  /**
   * NOTE: This is not a mapped field of entity metadata, just a simple property.
   * 
   * @Vich\UploadableField(mapping="announcementFile", fileNameProperty="filename")
   * @Assert\File(
   *    maxSize = "20M",
   *    mimeTypes = {"image/jpg", "image/jpeg", "image/png", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/msword", "text/plain", "application/pdf", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"},
   *    mimeTypesMessage = "- Invalid file type. Available file types are word documents, pdfs, images, and excel files."
   * )
   * 
   * @var File|null
   */
  private $announcementFile;


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

  public function getStartDate(): ?\DateTimeInterface
  {
    return $this->start_date;
  }

  public function setStartDate(?\DateTimeInterface $start_date): self
  {
    $this->start_date = $start_date;

    return $this;
  }

  public function getEndDate(): ?\DateTimeInterface
  {
    return $this->end_date;
  }

  public function setEndDate(?\DateTimeInterface $end_date): self
  {

    if ($end_date == null) {
      // if no argument provided, create it from continue date
      $end_date = clone $this->start_date;
      $end_date->modify('+'.$this->continue_date.'days');
      $this->end_date = $end_date;

    } else {
      $this->end_date = $end_date;

    }

    return $this; 

  }

  public function getContinueDate(): ?int
  {
    if ($this->start_date != null && $this->end_date != null) {
      if ($this->continue_date == null) {
        // intended to check if this is a new announcement or a modification
        // there will never be a start/end date for a new announcement. If both are null, then it's new.
        // if one or both exist, this is a modification, so retrieve the continue date value.
        $this->continue_date = $this->start_date->diff($this->end_date);
        $this->continue_date = $this->continue_date->format('%d');

      }
    }

    return $this->continue_date;

  }

  public function setContinueDate(int $continue_date): self
  {
    $this->continue_date = $continue_date;

    return $this;
  }

  public function getApproval(): ?int
  {
    return $this->approval;
  }

  public function setApproval(int $approval): self
  {
    $this->approval = $approval;

    return $this;
  }

  /**
   * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
   * of 'UploadedFile' is injected into this setter to trigger the update. If this
   * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
   * must be able to accept an instance of 'File' as the bundle will inject one here
   * during Doctrine hydration.
   *
   * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $announcementFile
   */
    public function setAnnouncementFile(?File $announcementFile = null): void
    {
      $this->announcementFile = $announcementFile;

      if (null !== $announcementFile) {
        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updatedAt = new \DateTimeImmutable();
      }
    }

    public function getAnnouncementFile(): ?File
    {
      return $this->announcementFile;
    }
  
    public function getFilename(): ?string
    {
      return $this->filename;
    }
  
    public function setFilename(?string $filename): void
    {
      $this->filename = $filename;
    }

}
