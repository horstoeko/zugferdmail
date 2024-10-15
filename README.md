# IMAP/POP3-Watcher for E-Documents

[![Latest Stable Version](https://img.shields.io/packagist/v/horstoeko/zugferdmail.svg?style=plastic)](https://packagist.org/packages/horstoeko/zugferdmail)
[![PHP version](https://img.shields.io/packagist/php-v/horstoeko/zugferdmail.svg?style=plastic)](https://packagist.org/packages/horstoeko/zugferdmail)
[![License](https://img.shields.io/packagist/l/horstoeko/zugferdmail.svg?style=plastic)](https://packagist.org/packages/horstoeko/zugferdmail)

[![Build Status](https://github.com/horstoeko/zugferdmail/actions/workflows/build.ci.yml/badge.svg)](https://github.com/horstoeko/zugferdmail/actions/workflows/build.ci.yml)
[![Release Status](https://github.com/horstoeko/zugferdmail/actions/workflows/build.release.yml/badge.svg)](https://github.com/horstoeko/zugferdmail/actions/workflows/build.release.yml)

## Table of Contents

- [IMAP/POP3-Watcher for E-Documents](#imappop3-watcher-for-e-documents)
  - [Table of Contents](#table-of-contents)
  - [Note](#note)
  - [License](#license)
  - [Overview](#overview)
  - [Dependencies](#dependencies)
  - [Installation](#installation)
  - [Usage](#usage)
    - [Configuration](#configuration)
    - [Check mail accounts for matching mails:](#check-mail-accounts-for-matching-mails)
    - [Predefined handlers](#predefined-handlers)
    - [Implement your own handler](#implement-your-own-handler)
    - [Console](#console)
      - [Start monitoring a mailbox](#start-monitoring-a-mailbox)

## Note

> [!CAUTION]
> This library is currently still considered experimental and should therefore be used with caution. I would be happy for an issue to be posted if bugs are found.

## License

The code in this project is provided under the [MIT](https://opensource.org/licenses/MIT) license.

## Overview

With this library it is possible to monitor mail accounts (IMAP and POP3) and to check and process incoming electronic invoice documents (ZUGFeRD PDF and XML).

## Dependencies

This package makes use of

* [horstoeko/zugferd](https://github.com/horstoeko/zugferd)
* [horstoeko/zugferdublbridge](https://github.com/horstoeko/zugferdublbridge)
* [Webklex/php-imap](https://github.com/Webklex/php-imap)

## Installation

There is one recommended way to install `horstoeko/zugferdmail` via [Composer](https://getcomposer.org/):

```bash
composer require horstoeko/zugferdmail
```

## Usage

For detailed eplanation you may have a look in the [examples](https://github.com/horstoeko/zugferdmail/tree/master/examples) of this package and the documentation attached to every release.

First, it is necessary to configure the library so that it is aware of the mail accounts to be checked. For IMAP accounts, the folders to be monitored must also be defined. In addition, any actions (handlers) can be defined for each mail account.

### Configuration

First you need to create a configuration instance:

```php
$config = new ZugferdMailConfig();
```

The mail accounts to be monitored must then be defined. You can also specify the folders and mimetypes to be checked:

```php
$account1 = $config->addAccount('demo', '192.168.1.1', 993, 'imap', 'ssl', false, 'demouser', 'demopassword');
$account1->addFolderToWatch('INBOX');
$account1->addFolderToWatch('somefolder/somesubfolder');
$account1->addMmimeTypeToWatch('application/pdf');
$account1->addMmimeTypeToWatch('text/xml');
```

Last but not least, the actions (handlers) to be performed are specified for each mail account, which are executed when a ZUGFeRD or XML document is found. Some of these handlers are already available, but you can also define your own actions:

```php
$account1->addHandler(new ZugferdMailHandlerMoveMessage('Invoice/Incoming'));
$account1->addHandler(new ZugferdMailHandlerSaveToFile('/tmp', 'invoice.att'));
```

### Check mail accounts for matching mails:

To perform the actual handling of all mail accounts, instantiate the class ```ZugferdMailReader```, to which the configuration is passed. The monitoring is started by calling the method ```checkAllAccounts```

```php
$reader = new ZugferdMailReader($config);
$reader->checkAllAccounts();
```

### Predefined handlers

| Class | Description |
| :------ | :------ |
| ZugferdMailHandlerNull | _Does not perform any operations_ |
| ZugferdMailHandlerCli | _Displays brief information about the e-invoice document found on the console_ |
| ZugferdMailHandlerCopyMessage | _Copies the message to another (different) directory_ |
| ZugferdMailHandlerMoveMessage| _Moves the message to another (different) directory_ |
| ZugferdMailHandlerDeleteMessage | _Deletes the message_ |
| ZugferdMailHandlerSaveToFile | _Saves the E-Document to a specified directory, optionally with a different filename_ |

### Implement your own handler

It is quite easy to implement your own action (handler). To do this, define your own class which extends a class from  ```ZugferdMailHandlerAbstract```. This abstract class defines a single method ```handleDocument```, which is passed information about the folder, message, attachment and the e-invoice document:

```php
public function handleDocument(
    ZugferdMailAccount $account,
    Folder $folder,
    Message $message,
    Attachment $attachment,
    ZugferdDocumentReader $document,
    int $recognitionType
);
```

An example:

```php

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

class MyOwnHandler extends ZugferdMailHandlerAbstract
{
    public function handleDocument(
        ZugferdMailAccount $account,
        Folder $folder,
        Message $message,
        Attachment $attachment,
        ZugferdDocumentReader $document,
        int $recognitionType)
    {
        // Do some stuff
    }
}
```

The parameter $recognitionType is one of the constants from ```ZugferdMailReaderRecognitionType```

| Name | Vakue | Description |
| :------ | :------ | :------ |
| ZFMAIL_RECOGNITION_TYPE_PDF | 0 | The document was recognized from a ZUGFeRD/Factur-X PDF attachment
| ZFMAIL_RECOGNITION_TYPE_XML | 1 | The document was recognized from a ZUGFeRD/Factur-X XML attachment
| ZFMAIL_RECOGNITION_TYPE_XML_UBL | 2 | The document was recognized from a ZUGFeRD/Factur-X XML attachment (in UBL-Syntax)

### Console

This library also includes a console application with which it is possible to carry out corresponding operations via shell. To get an overview of the existing commands from the “zfmail” namespace, use:

```sh
./vendor/bin/ZugferdMailConsole list
```

#### Start monitoring a mailbox

The command ``zfmail:processmailboxfolders`` is available for monitoring a mailbox via shell:

```sh
./vendor/bin/ZugferdMailConsole zfmail:processmailboxfolders [options]
```

This has the following parameters:

| Parameter | Description | Default | Multiple |
| :-------- | :---------- | :------ | :------: |
| host | The IP or hostname | | :x: |
| port | The port on the host | 993 | :x: |
| protocol | The protocol to use | imap | :x: |
| encryption | The encryption method to use | ssl | :x: |
| validateCert | If given the SSL certificates will be validated | false | :x: |
| username | The username to use for login | | :x: |
| password | The password to use for login | | :x: |
| authentication | The authentication method to use | | :x: |
| timeout | The connection timeout | 30 | :x: |
| folder | A folder to look into | | :heavy_check_mark: |
| mimetype | An attachment mimetype to check | | :heavy_check_mark: |
| handler | A handler to use | | :heavy_check_mark: |
| enableublsupport | If given the application will check for UBL syntax too | false | :x: |

Example usage

```bash
./vendor/bin/ZugferdMailConsole zfmail:processmailboxfolders \
  --host 127.0.0.1 \
  --port 993 \
  --username demouser \
  --password demopassword \
  --folder=INBOX \
  --mimetype=application/pdf \
  --mimetype=text/xml \
  --handler="horstoeko\zugferdmail\handlers\ZugferdMailHandlerCli" \
  --handler="horstoeko\zugferdmail\handlers\ZugferdMailHandlerSaveToFile,/tmp" \
  --enableublsupport
```
