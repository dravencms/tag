<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Tag\Repository;

use Dravencms\Locale\TLocalizedRepository;
use Dravencms\Model\Tag\Entities\Tag;
use Gedmo\Translatable\TranslatableListener;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Dravencms\Model\Locale\Entities\ILocale;

class TagRepository
{
    use TLocalizedRepository;
    /** @var \Kdyby\Doctrine\EntityRepository */
    private $tagRepository;

    /** @var EntityManager */
    private $entityManager;

    /**
     * TagRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->tagRepository = $entityManager->getRepository(Tag::class);
    }

    /**
     * @param $id
     * @return Tag[]
     */
    public function getById($id)
    {
        return $this->tagRepository->findBy(['id' => $id]);
    }

    /**
     * @param $id
     * @return null|Tag
     */
    public function getOneById($id)
    {
        return $this->tagRepository->find($id);
    }

    /**
     * @param $name
     * @return null|Tag
     */
    public function getOneByName($name)
    {
        return $this->tagRepository->findOneBy(['name' => $name]);
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
        $qb = $this->tagRepository->createQueryBuilder('t')
            ->select('t')
            ->where('t.name = :name')
            ->setParameters([
                'name' => $name
            ]);

        if ($ignoreTag)
        {
            $qb->andWhere('t != :ignoreTag')
                ->setParameter('ignoreTag', $ignoreTag);
        }

        $query = $qb->getQuery();
        $query->setHint(TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale->getLanguageCode());

        return (is_null($query->getOneOrNullResult()));
    }

    /**
     * @return \Kdyby\Doctrine\QueryBuilder
     */
    public function getTagQueryBuilder()
    {
        $qb = $this->tagRepository->createQueryBuilder('t')
            ->select('t');
        return $qb;
    }

    /**
     * @return array
     */
    public function getPairs()
    {
        return $this->tagRepository->findPairs('name');
    }

    /**
     * @param ILocale $locale
     * @return Tag[]
     */
    public function getAll(ILocale $locale)
    {
        $query = $this->tagRepository->createQueryBuilder('t')
            ->select('t')
            ->getQuery();

        return $this->getTranslatedResult($query, $locale);
    }

}