<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Dravencms\AdminModule\TagModule;

use Dravencms\AdminModule\Components\Tag\TagForm\TagFormFactory;
use Dravencms\AdminModule\Components\Tag\TagGrid\TagGridFactory;
use Dravencms\AdminModule\SecuredPresenter;
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

    public function renderDefault()
    {
        $this->template->h1 = 'Tags';
    }

    /**
     * @isAllowed(tag,edit)
     * @param $id
     * @throws \Nette\Application\BadRequestException
     */
    public function actionEdit($id)
    {
        if ($id) {
            $tag = $this->tagRepository->getOneById($id);

            if (!$tag) {
                $this->error();
            }

            $this->tag = $tag;

            $this->template->h1 = sprintf('Edit tag „%s“', $tag->getName());
        } else {
            $this->template->h1 = 'New tag';
        }
    }

    /**
     * @return \AdminModule\Components\Tag\TagForm
     */
    protected function createComponentFormTag()
    {
        $control = $this->tagFormFactory->create($this->tag);
        $control->onSuccess[] = function()
        {
            $this->flashMessage('Tag has been saved.', 'alert-success');
            $this->redirect('Tag:');
        };
        return $control;
    }

    /**
     * @return \AdminModule\Components\Tag\TagGrid
     */
    public function createComponentGridTag()
    {
        $control = $this->tagGridFactory->create();
        $control->onDelete[] = function()
        {
            $this->flashMessage('Tag has been deleted.', 'alert-success');
            $this->redirect('Tag:');
        };
        return $control;
    }
}
