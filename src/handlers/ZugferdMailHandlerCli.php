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
        if (php_sapi_name() !== 'cli') {
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

        $this->addLogMessage(sprintf("Found an document (%s) with number %s by date %s", $documenttypecode, $documentno, $documentdate->format("d.m.Y")));

        $this->addLogMessage(sprintf(" - Profile            %s", $document->getProfileDefinitionParameter('name')));
        $this->addLogMessage(sprintf(" - Invoice currency   %s", $invoiceCurrency));
        $this->addLogMessage(sprintf(" - Tay currency       %s", $taxCurrency));
        $this->addLogMessage(sprintf(" - Document name      %s", $documentname));
        $this->addLogMessage(sprintf(" - Document language  %s", $documentlanguage));

        if ($effectiveSpecifiedPeriod) {
            $this->addLogMessage(sprintf(" - Period             %s", $effectiveSpecifiedPeriod->format("d.m.Y")));
        }
    }
}
