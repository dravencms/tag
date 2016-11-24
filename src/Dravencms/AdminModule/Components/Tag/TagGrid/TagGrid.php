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

use Dravencms\Components\BaseGridFactory;
use App\Model\Locale\Repository\LocaleRepository;
use Dravencms\Model\Tag\Repository\TagRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;

/**
 * Description of TagGrid
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TagGrid extends Control
{

    /** @var BaseGridFactory */
    private $baseGridFactory;

    /** @var TagRepository */
    private $tagRepository;

    /** @var LocaleRepository */
    private $localeRepository;

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
     * @param LocaleRepository $localeRepository
     */
    public function __construct(TagRepository $tagRepository, BaseGridFactory $baseGridFactory, EntityManager $entityManager, LocaleRepository $localeRepository)
    {
        parent::__construct();

        $this->baseGridFactory = $baseGridFactory;
        $this->tagRepository = $tagRepository;
        $this->localeRepository = $localeRepository;
        $this->entityManager = $entityManager;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid
     */
    public function createComponentGrid($name)
    {
        $grid = $this->baseGridFactory->create($this, $name);

        $grid->setModel($this->tagRepository->getTagQueryBuilder());

        $grid->addColumnText('name', 'Name')
            ->setFilterText()
            ->setSuggestion();

        $grid->addColumnDate('updatedAt', 'Last edit', $this->localeRepository->getLocalizedDateTimeFormat())
            ->setSortable()
            ->setFilterDate();
        $grid->getColumn('updatedAt')->cellPrototype->class[] = 'center';

        if ($this->presenter->isAllowed('tag', 'edit')) {
            $grid->addActionHref('edit', 'Upravit')
                ->setIcon('pencil');
        }

        if ($this->presenter->isAllowed('tag', 'delete')) {
            $grid->addActionHref('delete', 'Smazat', 'delete!')
                ->setCustomHref(function($row){
                    return $this->link('delete!', $row->getId());
                })
                ->setIcon('trash-o')
                ->setConfirm(function ($row) {
                    return ['Opravdu chcete smazat tag %s ?', $row->getName()];
                });


            $operations = ['delete' => 'Smazat'];
            $grid->setOperation($operations, [$this, 'gridOperationsHandler'])
                ->setConfirm('delete', 'Opravu chcete smazat %i locales ?');
        }
        $grid->setExport();

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
