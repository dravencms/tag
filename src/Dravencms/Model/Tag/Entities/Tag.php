<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Tag\Entities;

use Doctrine\Common\Collections\ArrayCollection;
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
class Tag
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $identifier;

    /**
     * @var ArrayCollection|TagTranslation[]
     * @ORM\OneToMany(targetEntity="TagTranslation", mappedBy="tag",cascade={"persist", "remove"})
     */
    private $translations;

    /**
     * Tag constructor.
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        $this->identifier = $identifier;
        $this->translations = new ArrayCollection();
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return ArrayCollection|TagTranslation[]
     */
    public function getTranslations()
    {
        return $this->translations;
    }
}