<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Announcement", mappedBy="Category")
     */
    private $announcements;

    public function __construct()
    {
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAnnouncements(): ?Announcement
    {
        return $this->announcements;
    }

    public function addAnnouncement(Announcement $announcement): self
    {
        if (!$this->category->contains($announcement)) 
        {
            $this->category[] = $category;
            $category->setAnnouncement($this);
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): self
    {
        if ($this->category->removeElement($announcement)) 
        {
            // set the owning side to null (unless already changed)
            if ($category->getAnnouncement() === $this) 
            {
                $category->setAnnouncement(null);
            }
        }

        return $this;
    }
}
