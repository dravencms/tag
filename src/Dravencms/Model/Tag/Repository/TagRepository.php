<?php declare(strict_types = 1);
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Dravencms\Model\Tag\Repository;

use Dravencms\Model\Tag\Entities\Tag;
use Dravencms\Database\EntityManager;

class TagRepository
{
    /** @var \Doctrine\Persistence\ObjectRepository|Tag */
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
    public function getOneById(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    /**
     * @param array $parameters
     * @return Tag|null
     */
    public function getOneByParameters(array $parameters): ?Tag {
        return $this->tagRepository->findOneBy($parameters);
    }

    /**
     * @param string $identifier
     * @return Tag|null
     */
    public function getOneByIdentifier(string $identifier): ?Tag
    {
        return $this->tagRepository->findOneBy(['identifier' => $identifier]);
    }

    /**
     * @param string $identifier
     * @param Tag|null $ignoreTag
     * @return bool
     */
    public function isIdentifierFree(string $identifier, Tag $ignoreTag = null): bool
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
     * @return mixed
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
    public function getPairs(): array
    {
        return $this->tagRepository->findPairs('identifier');
    }
}
