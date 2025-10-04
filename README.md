# Mail template bundle

Mail template bundle helps to build and send emails from different source (Twig or Database).

## Installation

### Install via composer 

```bash
composer require schvoy/mail-template-bundle
```

### Define the required environment variables

* MAILER_SENDER_ADDRESS
* MAILER_SENDER_NAME
* MAILER_SIGNATORY

## Usage

### Twig based emails
                                    
Twig based emails can be easily used to send translated emails with a custom design. 

#### Creating new twig based email type

You just need to create your own `MailType`. Imagine `MailTypes` as different kind of emails such as: registration email, forgotten password email etc.
For each type of emails that you want to send you need to create a new `MailType`.

Twig based emails will automatically populated based on the `$translationKeyPath` value.

```php
<?php

declare(strict_types=1);

namespace App\Mails;

use Schvoy\MailTemplateBundle\Mailer\AbstractMailType;
use Schvoy\MailTemplateBundle\Mailer\Engine\TwigBased;

class TestMailType extends AbstractMailType
{
    use TwigBased;

    protected string $translationKeyPath = 'first_email.test';
}
```

How should translation file should look líke for the `TestMailType`:

```yaml
first_email: 
    test:
        subject: 'Test email'
        body: 'This is a test email'
        cta: 'Call to action button' # optional - in the `base_template.html.twig` is controlled by a privileged parameter `_ctaLink_`
```

> The bundle use by default the `MailTemplateBundle` translation domain

#### Override default email template

The bundle use a default email template from the bundle: `@MailTemplate/mail/base_template.html.twig`,
which is generated via mjml.

> You can also find the original mjml file here: `@MailTemplate/mail/base_template.html.twig.mjml`, 
> so you can generate your own base template with mjml.

You have two options to override it:

1. With bundle template override in your application: https://symfony.com/doc/current/bundles/override.html#templates

   > Make your own template for the following path: 'templates/bundles/MailTemplateBundle/mail/base_template.html.twig'

2. In your `MailType` override the `getTemplatePath` with your desired template path
    
   > With this option you can create more email templates to your project emails 

### Privileged parameters 

We call those parameters as privileged which are handled as not just a translation parameter during template rendering,
but those are also used for control (show or hide) template parts. 

In the base `base_template.html.twig` has two privileged parameters:

- `_ctaLink_` - To show or hide call to action button.
- `_greetingNameExist_` - To show greeting with/without name if name parameters exists/not exitss

#### Used conventions in `base_template.html.twig`

- %<parameter-name>% - normal parameter name for translations - eg.: `%userName%`
- _<parameter-name>_ - privileged parameter name for translations - eg.: `_ctaLink_`

### Sending an email

```php

use Schvoy\MailTemplateBundle\Mailer\Configuration;
use Symfony\Component\Mime\Email;

$testMail = $mailSender->getMailType(TestMailType::class);

$mailSender->send(
    $testMail,
    [
        new Recipient('test@example.com', 'Test user'),
        new Recipient('test-2@example.com'),
        new Recipient('cc@example.com', cc: true), 
        new Recipient('bcc@example.com', bcc: true),
    ],
    extendConfiguration: function (Configuration $configuration) {
        // Optional callback function to extend configuration before create email
        $configuration->addParameter('_ctaLink_', 'https://example.com');
    },
    extendEmail: function (Email $email) {
        // Optional callback function to extend email before send
        
        // Here you can also add attachments based on your needs    
    }
);
```

> MailSender creates the email for each "normal" (not cc or bcc) recipient. 

> If an email recipient signed as cc or bcc, those will assigned to all emails where the recipients are "normal" recipient. 

> At least one "normal" recipient is required 

## Configuration reference

```
mailer_template:
    translation_domain: <string>
```

## TODO

* Database based mail types
