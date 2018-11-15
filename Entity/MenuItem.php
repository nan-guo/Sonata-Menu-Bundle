<?php

namespace Prodigious\Sonata\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prodigious\Sonata\MenuBundle\Model\MenuItem as BaseMenuItem;

/**
 * @ORM\Entity(repositoryClass="Prodigious\Sonata\MenuBundle\Repository\MenuItemRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class MenuItem extends BaseMenuItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
}