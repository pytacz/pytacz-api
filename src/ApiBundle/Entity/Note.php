<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Note
 *
 * @ORM\Table(name="notes")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\NoteRepository")
 */
class Note
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=1000, nullable=true)
     */
    private $content;

    /**
     * @var bool
     *
     * @ORM\Column(name="askable", type="boolean")
     */
    private $askable;

    /**
     * @ORM\ManyToOne(targetEntity="Notebook", inversedBy="notes")
     * @ORM\JoinColumn(name="id_notebook", referencedColumnName="id", onDelete="CASCADE")
     */
    private $notebook;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Note
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Note
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set askable
     *
     * @param boolean $askable
     *
     * @return Note
     */
    public function setAskable($askable)
    {
        $this->askable = $askable;

        return $this;
    }

    /**
     * Get askable
     *
     * @return bool
     */
    public function getAskable()
    {
        return $this->askable;
    }

    /**
     * Set notebook
     *
     * @param \ApiBundle\Entity\Notebook $notebook
     *
     * @return Note
     */
    public function setNotebook(\ApiBundle\Entity\Notebook $notebook = null)
    {
        $this->notebook = $notebook;
        return $this;
    }

    /**
     * Get notebook
     *
     * @return \ApiBundle\Entity\Notebook
     */
    public function getNotebook()
    {
        return $this->notebook;
    }
}
