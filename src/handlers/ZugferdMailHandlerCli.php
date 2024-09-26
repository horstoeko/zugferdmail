<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\handlers;

use Webklex\PHPIMAP\Folder;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Attachment;
use horstoeko\zugferd\ZugferdDocumentReader;
use horstoeko\zugferdmail\config\ZugferdMailAccount;

/**
 * Class representing a handler that outputs information to CLI
 *
 * @category ZugferdMail
 * @package  ZugferdMail
 * @author   D. Erling <horstoeko@erling.com.de>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/horstoeko/zugferdmail
 */
class ZugferdMailHandlerCli implements ZugferdMailHandlerInterface
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

        $this->writeln("Found an invoice...");
        $this->writeln(sprintf(" - Invoice No. ............. %s", $documentno));
        $this->writeln(sprintf(" - Invoice Date ............ %s", $documentdate->format("d.m.Y")));
    }

    /**
     * Write to screen with CR at the end
     *
     * @param  string $str
     * @return void
     */
    protected function writeln(string $str): void
    {
        echo sprintf("%s\n", $str);
    }
}
