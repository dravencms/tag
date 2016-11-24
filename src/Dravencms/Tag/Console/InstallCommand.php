<?php

namespace Dravencms\Tag\Console;

use App\Model\Admin\Entities\Menu;
use App\Model\Admin\Repository\MenuRepository;
use App\Model\User\Entities\AclOperation;
use App\Model\User\Entities\AclResource;
use Kdyby\Doctrine\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

class InstallCommand extends Command
{
    protected function configure()
    {
        $this->setName('dravencms:tag:install')
            ->setDescription('Installs dravencms module');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var MenuRepository $adminMenuRepository */
        $adminMenuRepository = $this->getHelper('container')->getByType('App\Model\Admin\Repository\MenuRepository');

        /** @var EntityManager $entityManager */
        $entityManager = $this->getHelper('container')->getByType('Kdyby\Doctrine\EntityManager');

        try {

            $aclResource = new AclResource('tag', 'Tag');

            $entityManager->persist($aclResource);

            $aclOperationEdit = new AclOperation($aclResource, 'edit', 'Allows editation of Tag');
            $entityManager->persist($aclOperationEdit);
            $aclOperationDelete = new AclOperation($aclResource, 'delete', 'Allows deletion of Tag');
            $entityManager->persist($aclOperationDelete);

            $adminMenuRoot = new Menu('Tags', ':Admin:Tag:Tag', 'fa-tags', $aclOperationEdit);
            $entityManager->persist($adminMenuRoot);

            $output->writeLn('Module installed successfully');
            return 0; // zero return code means everything is ok

        } catch (\Exception $e) {
            $output->writeLn('<error>' . $e->getMessage() . '</error>');
            return 1; // non-zero return code means error
        }
    }
}