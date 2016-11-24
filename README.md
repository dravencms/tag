# Dravencms Tag module

This is a simple tag module for dravencms

## Instalation

The best way to install dravencms/tag is using  [Composer](http://getcomposer.org/):


```sh
$ composer require dravencms/tag:@dev
```

Then you have to register extension in `config.neon`.

```yaml
extensions:
	tag: Dravencms\Tag\DI\TagExtension
```
