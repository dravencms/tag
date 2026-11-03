<?php declare(strict_types = 1);

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
use Dravencms\Components\BaseGrid\Grid;
use Dravencms\Locale\CurrentLocaleResolver;
use Dravencms\Model\Locale\Entities\Locale;
use Dravencms\Model\Tag\Repository\TagRepository;
use Dravencms\Database\EntityManager;
use Nette\Security\User;
use Contributte\Datagrid\Column\Action\Confirmation\StringConfirmation;

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

    /** @var Locale */
    private $currentLocale;

    /** @var EntityManager */
    private $entityManager;

    /** @var User */
    private $user;

    /**
     * @var array
     */
    public $onDelete = [];

    /**
     * TagGrid constructor.
     * @param TagRepository $tagRepository
     * @param BaseGridFactory $baseGridFactory
     * @param EntityManager $entityManager
     * @param User $user
     * @param CurrentLocaleResolver $currentLocaleResolver
     * @throws \Exception
     */
    public function __construct(
        TagRepository $tagRepository,
        BaseGridFactory $baseGridFactory,
        EntityManager $entityManager,
        User $user,
        CurrentLocaleResolver $currentLocaleResolver
    )
    {
        $this->baseGridFactory = $baseGridFactory;
        $this->tagRepository = $tagRepository;
        $this->currentLocale = $currentLocaleResolver->getCurrentLocale();
        $this->entityManager = $entityManager;
        $this->user = $user;
    }


    /**
     * @param $name
     * @return \Dravencms\Components\BaseGrid\Grid
     * @throws \Contributte\Datagrid\Exception\DataGridException
     */
    public function createComponentGrid(string $name): Grid
    {
        $grid = $this->baseGridFactory->create($this, $name);
        $grid->setWritePermission('tag', 'edit');

        $grid->setDataSource($this->tagRepository->getTagQueryBuilder());

        $grid->addColumnText('identifier', 'tag.admin.field.identifier')
            ->setSortable()
            ->setFilterText();

        if ($this->user->isAllowed('tag', 'edit')) {
            $grid->addAction('edit', '')
                ->setIcon('pencil')
                ->setTitle('tag.admin.action.edit')
                ->setClass('btn btn-sm btn-primary');
        }

        if ($this->user->isAllowed('tag', 'delete')) {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('tag.admin.action.delete')
                ->setClass('btn btn-sm btn-danger ajax')
                ->setConfirmation(new StringConfirmation('tag.admin.confirm.delete', 'identifier'));
            $grid->addGroupAction('tag.admin.action.delete')->onSelect[] = [$this, 'handleDelete'];
        }

        $grid->addExportCsvFiltered('tag.admin.action.exportFiltered', 'tag_filtered.csv')
            ->setTitle('tag.admin.action.exportFiltered');
        $grid->addExportCsv('tag.admin.action.exportAll', 'tag_all.csv')
            ->setTitle('tag.admin.action.exportAll');

        return $grid;
    }

    /**
     * @param $id
     * @throws \Exception
     */
    #[\Dravencms\User\Attributes\IsAllowed('tag', 'delete')]
    #[\Dravencms\Security\Mutation]
    public function handleDelete($id): void
    {
        $this->requirePermission('tag', 'delete');
        $tags = $this->tagRepository->getById($id);
        foreach ($tags as $tag)
        {
            $this->entityManager->remove($tag);
        }

        $this->entityManager->flush();

        $this->onDelete();
    }

    public function render(): void
    {
        $template = $this->template;
        $template->setFile(__DIR__ . '/TagGrid.latte');
        $template->render();
    }
}
