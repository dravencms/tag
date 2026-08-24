# DravenCMS Tag

Multilingual tag administration and persistence for DravenCMS packages and applications.

## Features

- Stable machine-readable tag identifiers.
- Locale-specific names and descriptions.
- Admin form and searchable data grid.
- Repository APIs for identifiers, translations, pairs, and query builders.
- Admin menu and ACL fixtures.

## Installation

```bash
composer require dravencms/tag
```

The package loader registers the DI extension, admin presenter, services, translations, and Doctrine mappings. Apply its schema through the application migration workflow and load fixtures for the admin menu and `tag` ACL resource.

## Usage

Inject `Dravencms\Model\Tag\Repository\TagRepository` to query tags:

```php
$tag = $tagRepository->getOneByIdentifier('news');
$choices = $tagRepository->getPairs();
```

Use `TagTranslationRepository` when selecting or validating translated tag records for a specific locale. Persist relationships to the `Tag` entity, using translations only for display content.

Identifiers and translated names are validated for uniqueness by the administration forms.

## License

This package is licensed under the LGPL-3.0-only license.
