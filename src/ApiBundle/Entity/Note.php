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
     * @var array
     *
     * @ORM\Column(name="content", type="json_array")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="Notebook", inversedBy="notes")
     * @ORM\JoinColumn(name="id_notebook", referencedColumnName="id")
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
     * Set content
     *
     * @param array $content
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
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set user
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
     * Get user
     *
     * @return \ApiBundle\Entity\Notebook
     */
    public function getNotebook()
    {
        return $this->notebook;
    }
}
