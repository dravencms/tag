<?php

namespace Dravencms\Tag\Script;

use Dravencms\Model\Admin\Entities\Menu;
use Dravencms\Model\Admin\Repository\MenuRepository;
use Dravencms\Model\User\Entities\AclOperation;
use Dravencms\Model\User\Entities\AclResource;
use Dravencms\Model\User\Repository\AclOperationRepository;
use Dravencms\Model\User\Repository\AclResourceRepository;
use Dravencms\Packager\IPackage;
use Dravencms\Packager\IScript;
use Kdyby\Doctrine\EntityManager;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class PostInstall implements IScript
{
    /** @var MenuRepository */
    private $menuRepository;

    /** @var EntityManager */
    private $entityManager;

    /** @var AclOperationRepository */
    private $aclOperationRepository;

    /** @var AclResourceRepository */
    private $aclResourceRepository;

    /**
     * PostInstall constructor.
     * @param MenuRepository $menuRepository
     * @param EntityManager $entityManager
     * @param AclOperationRepository $aclOperationRepository
     * @param AclResourceRepository $aclResourceRepository
     */
    public function __construct(MenuRepository $menuRepository, EntityManager $entityManager, AclOperationRepository $aclOperationRepository, AclResourceRepository $aclResourceRepository)
    {
        $this->menuRepository = $menuRepository;
        $this->entityManager = $entityManager;
        $this->aclOperationRepository = $aclOperationRepository;
        $this->aclResourceRepository = $aclResourceRepository;
    }

    /**
     * @param IPackage $package
     * @throws \Exception
     */
    public function run(IPackage $package)
    {
        if (!$aclResource = $this->aclResourceRepository->getOneByName('tag')) {
            $aclResource = new AclResource('tag', 'Tag');

            $this->entityManager->persist($aclResource);
        }

        if (!$aclOperationEdit = $this->aclOperationRepository->getOneByName('edit')) {
            $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of Tag');
            $this->entityManager->persist($aclOperationEdit);
        }

        if (!$aclOperationDelete = $this->aclOperationRepository->getOneByName('delete')) {
            $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of Tag');
            $this->entityManager->persist($aclOperationDelete);
        }

        if (!$this->menuRepository->getOneByPresenter(':Admin:Tag:Tag')) {
            $adminMenuRoot = new Menu('Tags', ':Admin:Tag:Tag', 'fa-tags', $aclOperationEdit);
            $this->entityManager->persist($adminMenuRoot);
        }

        $this->entityManager->flush();
    }
}