<?php declare(strict_types = 1);

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dravencms\AdminModule\TagModule;

use Dravencms\AdminModule\Components\Tag\TagForm\TagForm;
use Dravencms\AdminModule\Components\Tag\TagForm\TagFormFactory;
use Dravencms\AdminModule\Components\Tag\TagGrid\TagGrid;
use Dravencms\AdminModule\Components\Tag\TagGrid\TagGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
use Dravencms\Flash;
use Dravencms\Model\Tag\Entities\Tag;
use Dravencms\Model\Tag\Repository\TagRepository;

/**
 * Description of TagPresenter
 *
 * @author Adam Schubert
 */
class TagPresenter extends SecuredPresenter
{
    /** @var TagRepository @inject */
    public $tagRepository;

    /** @var TagFormFactory @inject */
    public $tagFormFactory;

    /** @var TagGridFactory @inject */
    public $tagGridFactory;

    /** @var null|Tag */
    private $tag = null;

    public function renderDefault(): void
    {
        $this->template->h1 = $this->translator->translate('tag.admin.title.tags');
    }

    /**
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    #[\Dravencms\User\Attributes\IsAllowed('tag', 'edit')]
    public function actionEdit(?int $id = null): void
    {
        if ($id) {
            $tag = $this->tagRepository->getOneById($id);

            if (!$tag) {
                $this->error();
            }

            $this->tag = $tag;

            $this->template->h1 = $this->translator->translate('tag.admin.title.edit', ['identifier' => $tag->getIdentifier()]);
        } else {
            $this->template->h1 = $this->translator->translate('tag.admin.title.create');
        }
    }

    /**
     * @return TagForm
     */
    protected function createComponentFormTag(): TagForm
    {
        $control = $this->tagFormFactory->create($this->tag);
        $control->onSuccess[] = function()
        {
            $this->flashMessage($this->translator->translate('tag.admin.flash.saved'), Flash::SUCCESS);
            $this->redirect('Tag:');
        };
        return $control;
    }

    /**
     * @return TagGrid
     */
    public function createComponentGridTag(): TagGrid
    {
        $control = $this->tagGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage($this->translator->translate('tag.admin.flash.deleted'), Flash::SUCCESS);
            $this->redirect('Tag:');
        };
        return $control;
    }
}
