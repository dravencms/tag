<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Tag\Repository;

use Dravencms\Locale\TLocalizedRepository;
use Dravencms\Model\Tag\Entities\Tag;
use Dravencms\Model\Tag\Entities\TagTranslation;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Dravencms\Model\Locale\Entities\ILocale;

class TagTranslationRepository
{
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $tagTranslationRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * TagRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->tagTranslationRepository = $entityManager->getRepository(TagTranslation::class);
    }

    /**
     * @param $id
     * @return TagTranslation[]
     */
    public function getById($id)
    {
        return $this->tagTranslationRepository->findBy(['id' => $id]);
    }

    /**
     * @param $id
     * @return null|TagTranslation
     */
    public function getOneById($id)
    {
        return $this->tagTranslationRepository->find($id);
    }

    /**
     * @param $name
     * @return null|TagTranslation
     */
    public function getOneByName($name)
    {
        return $this->tagTranslationRepository->findOneBy(['name' => $name]);
    }

    /**
     * @param $name
     * @param ILocale $locale
     * @param Tag|null $ignoreTag
     * @return boolean
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isNameFree($name, ILocale $locale, Tag $ignoreTag = null)
    {
        $qb = $this->tagTranslationRepository->createQueryBuilder('tt')
            ->select('tt')
            ->join('tt.tag', 't')
            ->where('tt.name = :name')
            ->andWhere('tt.locale = :locale')
            ->setParameters([
                'name' => $name,
                'locale' => $locale
            ]);

        if ($ignoreTag)
        {
            $qb->andWhere('t != :ignoreTag')
                ->setParameter('ignoreTag', $ignoreTag);
        }

        $query = $qb->getQuery();

        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @param Tag $tag
     * @param ILocale $locale
     * @return null|TagTranslation
     */
    public function getTranslation(Tag $tag, ILocale $locale)
    {
        return $this->tagTranslationRepository->findOneBy(['tag' => $tag, 'locale' => $locale]);
    }

    /**
     * @param ILocale $locale
     * @return TagTranslation[]
     */
    public function getAll(ILocale $locale)
    {
        return $this->tagTranslationRepository->findBy(['locale' => $locale]);
    }
}