<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderExport
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderExport\Model\Config\Source;

use Mageplaza\OrderExport\Model\Config\AbstractSource;

/**
 * Class FileType
 * @package Mageplaza\OrderExport\Model\Config\Source
 */
class FileType extends AbstractSource
{
    const XML       = 'xml';
    const CSV       = 'csv';
    const TXT       = 'txt';
    const EXCEL_XML = 'excel_xml';
    const TSV       = 'tsv';
    const JSON      = 'json';
    const ODS       = 'ods';
    const XLSX      = 'xlsx';

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::XML       => 'XML',
            self::CSV       => 'CSV',
            self::TXT       => 'TXT',
            self::EXCEL_XML => 'Excel XML',
            self::TSV       => 'TSV',
            self::JSON      => 'JSON',
            self::ODS       => 'ODS',
            self::XLSX      => 'XLSX',
        ];
    }
}
