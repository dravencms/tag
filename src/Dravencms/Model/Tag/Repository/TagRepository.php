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
     * @return Tag[]
     */
    public function getAll()
    {
        return $this->tagRepository->findAll();
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
     * @param array $parameters
     * @return Tag|null
     */
    public function getOneByParameters(array $parameters) {
        return $this->tagRepository->findOneBy($parameters);
    }
    
    /**
     * @param $identifier
     * @return null|Tag
     */
    public function getOneByIdentifier($identifier)
    {
        return $this->tagRepository->findOneBy(['identifier' => $identifier]);
    }

    /**
     * @param $identifier
     * @param Tag|null $ignoreTag
     * @return boolean
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isIdentifierFree($identifier, Tag $ignoreTag = null)
    {
        $qb = $this->tagRepository->createQueryBuilder('t')
            ->select('t')
            ->where('t.identifier = :identifier')
            ->setParameters([
                'identifier' => $identifier
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
        return $this->tagRepository->findPairs('identifier');
    }
}
