<?php

/**
 * This file is a part of horstoeko/zugferdmail.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace horstoeko\zugferdmail\consts;

class ZugferdMailReaderRecognitionType
{
    /**
     * Recognized from a ZUGFeRD/Factur-X PDF
     */
    public const ZFMAIL_RECOGNITION_TYPE_PDF = 0;

    /**
     * Recognized from a XRechnung or EN16931-compatible XML
     */
    public const ZFMAIL_RECOGNITION_TYPE_XML = 1;

    /**
     * Recognized from a XRechnung or EN16931-compatible XML in UBL-Syntax
     */
    public const ZFMAIL_RECOGNITION_TYPE_XML_UBL = 2;
}