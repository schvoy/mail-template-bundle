# Changelog

## 1.0.0

* Introduce a new `Configuration` class instead of configuration array 
* Improve `MailSender` with `$extendConfiguration` and `$extendEmail` callback options to allow more option for customization 
* Rework `TwigBased` trait
* Add mjml base template to generate more standard email base templates
* Remove `email_base_template` configuration option, to use Symfony template overriding (the right way)
* Remove `email_base_css_template` configuration option, to remove extra unnecessary dependency
* Remove dependency `tijsverkoyen/css-to-inline-styles`
* Add support for cc and bcc emails
* Add additional tests
* Add Doctrine based email types support

## 0.7.0

* Move repository from eightmarq/mail-template-bundle to schvoy/mail-template-bundle
* Bump composer packages, increase minimum versions (php to 8.3, Symfony to 7.1)
* Replace eightmarq/core-bundle with schvoy/base-entity-bundle
* Update code because of package bumps
* Improve AbstractMailType 
  * add configuration property
  * remove unnecessary MailEngineInterface interface 
* Add test environment for PhpUnit tests
* Add tests for TwigBasedEmails
* Add `before-commit`, `code-quality` and `tests` composer scripts
* Allow to change email template for the email templates one by one
* Update README.md

## 0.6.1

* Update composer.json requirements

## 0.6.0

* Update required Symfony version to 6.1 and PHP version to 8.1

## 0.5.2

* Use empty string instead of null value as sender name when it's not defined
* Fix typo in change log 

## 0.5.1

* Use empty string instead of null value as recipient name when it's not defined
* Add install information to README.md

## 0.5.0

* Add Twig based email types support
* Add basic email template layout 
* Add convert css file to inline css for emails 