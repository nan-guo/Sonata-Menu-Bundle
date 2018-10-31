<?php

namespace Prodigious\Sonata\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Prodigious\Sonata\MenuBundle\Model\MenuAbstract;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Menu
 *
 * @ORM\Table(name="sonata_menu", indexes={
 *     @ORM\Index(name="alias_idx", columns={"alias"})
 * })
 * @ORM\Entity(repositoryClass="Prodigious\Sonata\MenuBundle\Repository\MenuRepository")
 * @UniqueEntity("alias")
 */
class Menu extends MenuAbstract
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
}
