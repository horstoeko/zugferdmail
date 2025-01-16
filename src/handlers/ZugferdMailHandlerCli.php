<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;

/**
 * Class representing a handler that outputs information to CLI
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerCli extends ZugferdMailHandlerAbstract
{
    /**
     * @inheritDoc
     */
    public function handleDocument(ZugferdMailAccount $account, Folder $folder, Message $message, Attachment $attachment, ZugferdDocumentReader $document, int $recognitionType)
    {
        if (PHP_SAPI !== 'cli') {
            return;
        }

        $document->getDocumentInformation(
            $documentno,
            $documenttypecode,
            $documentdate,
            $invoiceCurrency,
            $taxCurrency,
            $documentname,
            $documentlanguage,
            $effectiveSpecifiedPeriod
        );

        $this->addLogMessageToMessageBag(sprintf("Found an document (%s) with number %s by date %s", $documenttypecode, $documentno, $documentdate->format("d.m.Y")));

        $this->addLogMessageToMessageBag(sprintf(" - Profile            %s", $document->getProfileDefinitionParameter('name')));
        $this->addLogMessageToMessageBag(sprintf(" - Invoice currency   %s", $invoiceCurrency));
        $this->addLogMessageToMessageBag(sprintf(" - Tay currency       %s", $taxCurrency));
        $this->addLogMessageToMessageBag(sprintf(" - Document name      %s", $documentname));
        $this->addLogMessageToMessageBag(sprintf(" - Document language  %s", $documentlanguage));

        if ($effectiveSpecifiedPeriod !== null) {
            $this->addLogMessageToMessageBag(sprintf(" - Period             %s", $effectiveSpecifiedPeriod->format("d.m.Y")));
        }
    }
}
