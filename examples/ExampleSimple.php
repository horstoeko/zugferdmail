<?php

use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Attachment;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\ZugferdMailReader;
use horstoeko\zugferdmail\config\ZugferdMailConfig;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use horstoeko\zugferdmail\handlers\ZugferdMailHandlerSaveToFile;

require_once dirname(__FILE__) . "/../vendor/autoload.php";
require_once dirname(__FILE__) . '/ExampleHelper.php';

$config = new ZugferdMailConfig();

$account = ExampleHelper::createMailAccountFromDotEnv($config);

$account->addHandler(new ZugferdMailHandlerSaveToFile('/tmp', 'file.xml'));

$account->addCallback(function (ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType) {
    $document->getDocumentInformation($documentno, $documenttypecode, $documentdate, $invoiceCurrency, $taxCurrency, $documentname, $documentlanguage, $effectiveSpecifiedPeriod);
    echo "Document found ... " . PHP_EOL;
    echo "Document No. ..... " . $documentno . PHP_EOL;
});

$reader = new ZugferdMailReader($config);
$reader->checkAllAccounts();
