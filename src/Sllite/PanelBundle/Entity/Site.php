<?php

namespace Sllite\PanelBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sllite\PanelBundle\Model\SiteInterface;

/**
 * Сайт.
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Site implements SiteInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="domain", type="string", length=255)
     */
    private $domain;


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain()
    {
        return $this->domain;
    }
}
