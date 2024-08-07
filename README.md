# Mail template bundle

Mail template bundle helps to build and send emails from different source (Twig or Database).

## Installation

### Install via composer 
```bash
composer require schvoy/mail-template-bundle
```

### Register templates in twig configuration 

```yaml 
twig:
    ...
    paths:
        '%kernel.project_dir%/vendor/schvoy/mail-template-bundle/src/Resources/views': MailTemplateBundle
```

## Define the required environment variables

* MAILER_SENDER_ADDRESS
* MAILER_SENDER_NAME
* MAILER_SIGNATORY

## Usage

### Twig based emails

Basically if you are using Twig based emails, the bundle will use a default email template (`email_base_template`).
You need to define only `subject` and `body` parameters. 
These can be texts or translation keys.

If you use custom email template (override: `email_base_template` configuration) you can add more parameters to your MailType(s),
and these parameters will be reachable in your template. 

```twig
    {{ __mailType.subject }}
    {{ __mailType.body }}
    {{ __mailType.customParameter }}
```

> Parameters have to have public access or a public method to reach it

#### Creating new twig based mail type

```php
<?php

declare(strict_types=1);

namespace App\Mails;

use Schvoy\MailTemplateBundle\Mailer\AbstractMailType;
use Schvoy\MailTemplateBundle\Mailer\Engine\TwigBased;

class TestMailType extends AbstractMailType
{
    use TwigBased;

    protected string $subject = 'first_email.test.subject'; // Translation key

    protected string $body = 'first_email.test.body'; // Translation key
}
```

### Sending an email

```php
$testMail = $mailSender->getMailType(TestMailType::class);

$mailSender->send(
    $testMail,
    [
        new Recipient('test@example.com', 'Test user'),
        new Recipient('test-2@example.com'),
    ],
    [
        'parameters' => [
            '%test%' => 'Test parameter',
        ],
    ]
);
```

### Default email (template) parameters

```php
[
    '__greeting' => true,
    '__signature' => true,
    '__userName' => $recipient->getName() ?? false,
    '__mailType' => $mailType,
    '__translationDomain' => $this->parameterBag->get(
        sprintf('%s.%s', MailTemplateBundleExtension::ALIAS, 'translation_domain')
    ),
    '__locale' => 'en',
    'parameters' => [
        '%userName%' => $recipient->getName(),
        '%signatory%' => $this->parameterBag->get('mailer_signatory'),
    ],
]
```

## Configuration reference

```
mailer_template_bundle:
    translation_domain: <string>
    email_base_template: <string>
    email_base_css_template: <string>
```

## Not supported yet - TODO

* Database based mail types
* CC and BCC 
* Attachment for mails
