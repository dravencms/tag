<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Tag\Entities;

use Dravencms\Model\Article\Entities\Article;
use Dravencms\Model\Gallery\Entities\Picture;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class Tag
 * @package App\Model\Structure\Entities
 * @ORM\Entity
 * @ORM\Table(name="tagTag")
 */
class Tag extends Nette\Object
{
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @Gedmo\Translatable
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection|Article[]
     *
     * @ORM\ManyToMany(targetEntity="\Dravencms\Model\Article\Entities\Article", mappedBy="tags")
     */
    private $articles;

    /**
     * @var \Doctrine\Common\Collections\Collection|Picture[]
     *
     * @ORM\ManyToMany(targetEntity="\Dravencms\Model\Gallery\Entities\Picture", mappedBy="tags")
     */
    private $galleryPictures;

    /**
     * Tag constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }


    /**
     * @param Article $article
     */
    public function addArticle(Article $article)
    {
        if ($this->articles->contains($article))
        {
            return;
        }
        $this->articles->add($article);
        $article->addTag($this);
    }

    /**
     * @param Article $article
     */
    public function removeArticle(Article $article)
    {
        if (!$this->articles->contains($article))
        {
            return;
        }
        $this->articles->removeElement($article);
        $article->removeTag($this);
    }


    /**
     * @param Picture $picture
     */
    public function addGalleryPicture(Picture $picture)
    {
        if ($this->galleryPictures->contains($picture))
        {
            return;
        }
        $this->galleryPictures->add($picture);
        $picture->addTag($this);
    }

    /**
     * @param Picture $picture
     */
    public function removeGalleryPicture(Picture $picture)
    {
        if (!$this->galleryPictures->contains($picture))
        {
            return;
        }
        $this->galleryPictures->removeElement($picture);
        $picture->removeTag($this);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}