#[Frio](http://frio.sk/en) coding-standard

Customized coding standards for [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) using some our own created standards and some from [Slevomat](https://github.com/slevomat/coding-standard).

## Instalation
1. Install [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) using [Composer](https://getcomposer.org)
```sh
$ composer require squizlabs/php_codesniffer
```
2. Copy content of Standards folder to /vendor/squizlabs/php_codesniffer/CodeSniffer/Standards

3. Set FrioStandard as default coding standard
```sh
$ phpcs --config-set default_standard FrioStandard
```
4. You're done! Let's check and clean up your code :)
