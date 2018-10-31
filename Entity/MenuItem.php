<?php

namespace Prodigious\Sonata\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Prodigious\Sonata\MenuBundle\Entity\Menu;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Entity\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemAbtract;

/**
 * MenuItem
 *
 * @ORM\Table(name="sonata_menu_item")
 * @ORM\Entity(repositoryClass="Prodigious\Sonata\MenuBundle\Repository\MenuItemRepository")
 */
class MenuItem extends MenuItemAbtract
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
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
}
