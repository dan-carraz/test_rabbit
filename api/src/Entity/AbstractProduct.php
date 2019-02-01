<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractProduct
 * @package App\Entity
 *
 * @ORM\MappedSuperclass()
 */
class AbstractProduct
{
    /**
     * @var string
     */
    protected $id;

     /**
     * @var string
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank()
     * @Assert\Length(max="20")
     */
    protected $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $enabled = false;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AbstractProduct
     */
    public function setName(string $name): AbstractProduct
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}