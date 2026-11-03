# DravenCMS Tag

Multilingual tag administration and persistence for DravenCMS packages and applications.

## Features

- Stable machine-readable tag identifiers.
- Locale-specific names and descriptions.
- Admin form and searchable data grid.
- Repository APIs for identifiers, translations, pairs, and query builders.
- Provider-defined admin navigation and ACL permissions.

## Installation

```bash
composer require dravencms/tag
```

The package loader registers the DI extension, admin presenter, services, translations, and Doctrine mappings. Apply its schema through the application migration workflow before enabling administration. Navigation and ACL definitions come from providers.

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

## Admin authorization and mutations

Administration uses User 3.1, Admin 3.2 and Application 0.3 guards. Screens and write callbacks enforce the existing provider-defined permissions server-side, including direct URLs and bulk callbacks. Mutating signals require POST and a session-bound action token; synchronize the matching admin assets when upgrading. See the User package's `user:security:migrate` instructions for the password-reset schema change.
