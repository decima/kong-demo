<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 */
class Service
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
     * @ORM\Column(type="string", length=255)
     */
    private $internalUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $publicUrl;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $kongId;

    /**
     * @ORM\OneToMany(targetEntity=Usage::class, mappedBy="service")
     */
    private $usages;

    public function __construct()
    {
        $this->usages = new ArrayCollection();
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

    public function getInternalUrl(): ?string
    {
        return $this->internalUrl;
    }

    public function setInternalUrl(string $internalUrl): self
    {
        $this->internalUrl = $internalUrl;

        return $this;
    }

    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }

    public function setPublicUrl(?string $publicUrl): self
    {
        $this->publicUrl = $publicUrl;

        return $this;
    }

    public function getKongId(): ?string
    {
        return $this->kongId;
    }

    public function setKongId(string $kongId): self
    {
        $this->kongId = $kongId;

        return $this;
    }

    /**
     * @return Collection|Usage[]
     */
    public function getUsages(): Collection
    {
        return $this->usages;
    }

    public function addUsage(Usage $usage): self
    {
        if (!$this->usages->contains($usage)) {
            $this->usages[] = $usage;
            $usage->setService($this);
        }

        return $this;
    }

    public function removeUsage(Usage $usage): self
    {
        if ($this->usages->removeElement($usage)) {
            // set the owning side to null (unless already changed)
            if ($usage->getService() === $this) {
                $usage->setService(null);
            }
        }

        return $this;
    }
}
