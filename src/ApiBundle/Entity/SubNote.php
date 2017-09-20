<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubNote
 *
 * @ORM\Table(name="sub_notes")
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\SubNoteRepository")
 */
class SubNote
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
     * @ORM\ManyToOne(targetEntity="Note", inversedBy="sub_notes", cascade={"remove"})
     * @ORM\JoinColumn(name="id_note", referencedColumnName="id", onDelete="CASCADE")
     */
    private $note;

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
     * @return SubNote
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
     * @return SubNote
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
     * @return SubNote
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
     * Set note
     *
     * @param \ApiBundle\Entity\Note $note
     *
     * @return SubNote
     */
    public function setNote(\ApiBundle\Entity\Note $note = null)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Get note
     *
     * @return \ApiBundle\Entity\Note
     */
    public function getNote()
    {
        return $this->note;
    }
}
