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

namespace Dravencms\AdminModule\Components\Tag\TagForm;

use Dravencms\Components\BaseControl\BaseControl;
use Dravencms\Components\BaseForm\BaseFormFactory;
use Dravencms\Model\Locale\Repository\LocaleRepository;
use Dravencms\Model\Tag\Entities\Tag;
use Dravencms\Model\Tag\Entities\TagTranslation;
use Dravencms\Model\Tag\Repository\TagRepository;
use Dravencms\Model\Tag\Repository\TagTranslationRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;

/**
 * Description of TagForm
 *
 * @author Adam Schubert <adam.schubert@sg1-game.net>
 */
class TagForm extends BaseControl
{
    /** @var BaseFormFactory */
    private $baseFormFactory;

    /** @var EntityManager */
    private $entityManager;

    /** @var TagRepository */
    private $tagRepository;

    /** @var LocaleRepository */
    private $localeRepository;

    /** @var TagTranslationRepository */
    private $tagTranslationRepository;

    /** @var Tag */
    private $tag = null;

    /** @var array */
    public $onSuccess = [];

    /**
     * TagForm constructor.
     * @param BaseFormFactory $baseFormFactory
     * @param EntityManager $entityManager
     * @param TagRepository $tagRepository
     * @param TagTranslationRepository $tagTranslationRepository
     * @param LocaleRepository $localeRepository
     * @param Tag|null $tag
     */
    public function __construct(
        BaseFormFactory $baseFormFactory,
        EntityManager $entityManager,
        TagRepository $tagRepository,
        TagTranslationRepository $tagTranslationRepository,
        LocaleRepository $localeRepository,
        Tag $tag = null
    ) {
        parent::__construct();

        $this->tag = $tag;
        $this->baseFormFactory = $baseFormFactory;
        $this->entityManager = $entityManager;
        $this->tagRepository = $tagRepository;
        $this->tagTranslationRepository = $tagTranslationRepository;
        $this->localeRepository = $localeRepository;


        if ($this->tag) {
            $defaults = [
                'identifier' => $this->tag->getIdentifier()
            ];

            foreach ($this->tag->getTranslations() AS $translation)
            {
                $defaults[$translation->getLocale()->getLanguageCode()]['name'] = $translation->getName();
                $defaults[$translation->getLocale()->getLanguageCode()]['description'] = $translation->getDescription();
            }

            $this['form']->setDefaults($defaults);
        }
    }

    /**
     * @return \Dravencms\Components\BaseForm\BaseForm
     */
    protected function createComponentForm()
    {
        $form = $this->baseFormFactory->create();

        foreach ($this->localeRepository->getActive() as $activeLocale) {
            $container = $form->addContainer($activeLocale->getLanguageCode());
            $container->addText('name')
                ->setRequired('Please enter tag name.')
                ->addRule(Form::MAX_LENGTH, 'Tag name name is too long.', 255);

            $container->addTextArea('description')
                ->setRequired('Please enter description.')
                ->addRule(Form::MAX_LENGTH, 'Description message is too long.', 2000);
        }

        $form->addText('identifier')
            ->setRequired('Please fill in an identifier');

        $form->addSubmit('send');

        $form->onValidate[] = [$this, 'editFormValidate'];
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    /**
     * @param Form $form
     */
    public function editFormValidate(Form $form)
    {
        $values = $form->getValues();

        if (!$this->tagRepository->isIdentifierFree($values->identifier, $this->tag)) {
            $form->addError('Tento identifier je již zabrán.');
        }

        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if (!$this->tagTranslationRepository->isNameFree($values->{$activeLocale->getLanguageCode()}->name, $activeLocale, $this->tag)) {
                $form->addError('Tento název je již zabrán.');
            }
        }

        if (!$this->presenter->isAllowed('tag', 'edit')) {
            $form->addError('Nemáte oprávění editovat tag.');
        }
    }

    /**
     * @param Form $form
     * @throws \Exception
     */
    public function editFormSucceeded(Form $form)
    {
        $values = $form->getValues();

        if ($this->tag) {
            $tag = $this->tag;
            $tag->setIdentifier($values->identifier);
        } else {
            $tag = new Tag($values->identifier);
        }

        $this->entityManager->persist($tag);

        $this->entityManager->flush();
        
        foreach ($this->localeRepository->getActive() AS $activeLocale) {
            if ($bonusTranslation = $this->tagTranslationRepository->getTranslation($tag, $activeLocale))
            {
                $bonusTranslation->setName($values->{$activeLocale->getLanguageCode()}->name);
                $bonusTranslation->setDescription($values->{$activeLocale->getLanguageCode()}->description);
            }
            else
            {
                $bonusTranslation = new TagTranslation(
                    $tag,
                    $activeLocale,
                    $values->{$activeLocale->getLanguageCode()}->name,
                    $values->{$activeLocale->getLanguageCode()}->description
                );
            }
            $this->entityManager->persist($bonusTranslation);
        }
        $this->entityManager->flush();

        $this->onSuccess();
    }

    public function render()
    {
        $template = $this->template;
        $template->activeLocales = $this->localeRepository->getActive();
        $template->setFile(__DIR__ . '/TagForm.latte');
        $template->render();
    }
}
