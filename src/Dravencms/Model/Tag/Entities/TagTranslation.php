<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Tag\Entities;

use Dravencms\Model\Article\Entities\Article;
use Dravencms\Model\Gallery\Entities\Picture;
use Dravencms\Model\Locale\Entities\Locale;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette;

/**
 * Class Tag
 * @package App\Model\Structure\Entities
 * @ORM\Entity
 * @ORM\Table(name="tagTagTranslation")
 */
class TagTranslation
{
    use Nette\SmartObject;
    use Identifier;
    use TimestampableEntity;

    /**
     * @var string
     * @ORM\Column(type="string",length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text",nullable=false)
     */
    private $description;

    /**
     * @var Tag
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="translations")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     */
    private $tag;
    /**
     * @var Locale
     * @ORM\ManyToOne(targetEntity="Dravencms\Model\Locale\Entities\Locale")
     * @ORM\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    private $locale;

    /**
     * TagTranslation constructor.
     * @param Tag $tag
     * @param Locale $locale
     * @param string $name
     * @param string $description
     */
    public function __construct(Tag $tag, Locale $locale, string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
        $this->tag = $tag;
        $this->locale = $locale;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @param Tag $tag
     */
    public function setTag(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * @param Locale $locale
     */
    public function setLocale(Locale $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Tag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @return Locale
     */
    public function getLocale()
    {
        return $this->locale;
    }




}
