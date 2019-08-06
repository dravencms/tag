<?php

/*
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301  USA
 */

namespace Dravencms\AdminModule\Components\Tag\TagGrid;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseGrid\BaseGridFactory;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Dravencms\Model\Tag\Repository\TagRepository;
use Kdyby\Doctrine\EntityManager;

/**
 * Description of TagGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TagGrid extends BaseControl
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var TagRepository */
    private $tagRepository;

    /** @var CurrentLocale */
    private $currentLocale;

    /** @var EntityManager */
    private $entityManager;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * RobotsGrid constructor.
     * @param TagRepository $tagRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param CurrentLocaleResolver $currentLocaleResolver
     */
    public function __construct(
        TagRepository $tagRepository,
        BaseGridFactory $baseGridFactory,
        EntityManager $entityManager,
        CurrentLocaleResolver $currentLocaleResolver
    )
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->tagRepository = $tagRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setDataSource($this->tagRepository->getTagQueryBuilder());

        $grid->addColumnText('identifier', 'Identifier')
            ->setSortable()
            ->setFilterText();

        if ($this->presenter->isAllowed('tag', 'edit')) {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('Upravit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->presenter->isAllowed('tag', 'delete')) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Smazat')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirm('Do you really want to delete row %s?', 'name');
            $grid->addGroupAction('Smazat')->onSelect[] = [$this, 'handleDelete'];
        }

        $grid->addExportCsvFiltered('Csv export (filtered)', 'tag_filtered.csv')
            ->setTitle('Csv export (filtered)');
        $grid->addExportCsv('Csv export', 'tag_all.csv')
            ->setTitle('Csv export');

        return $grid;
    }

    /**
     * @param $action
     * @param $ids
     */
    public function gridOperationsHandler($action, $ids)
    {
        switch ($action)
        {
            case 'delete':
                $this->handleDelete($ids);
                break;
        }
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function handleDelete($id)
    {
        $tags = $this->tagRepository->getById($id);
        foreach ($tags AS $tag)
        {
            $this->entityManager->remove($tag);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render()
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TagGrid.latte');
        $template->render();
    }
}
