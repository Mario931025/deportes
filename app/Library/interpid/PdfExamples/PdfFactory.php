<?php

/**
 * This file is part of the Interpid PDF Addon package.
 *
 * @author Interpid <office@interpid.eu>
 * @copyright (c) Interpid, http://www.interpid.eu
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Interpid\PdfExamples;

if (!defined('PDF_RESOURCES_IMAGES')) {
    define('PDF_RESOURCES_IMAGES', __DIR__ . '/images');
}

use Interpid\PdfLib\Pdf;

/**
 * Pdf Factory
 * Contains functions that creates and initializes the PDF class
 *
 * @package Interpid\PdfExamples
 */
class PdfFactory
{
    /**
     * Creates a new PDF Object and Initializes it
     *
     * @param $type
     * @return MyPdf
     */
    public static function newPdf($type)
    {
        $pdf = new MyPdf();

        switch ($type) {
            case 'multicell':
                $pdf->setHeaderSource('header-multicell.txt');
                break;
            case 'table':
                $pdf->setHeaderSource('header-table.txt');
                break;
        }

        //initialize the pdf document
        self::initPdf($pdf);

        return $pdf;
    }

    /**
     * Initializes the pdf object.
     * Set the margins, adds a page, adds default fonts etc...
     *
     * @param Pdf $pdf
     * @return Pdf $pdf
     */
    public static function initPdf($pdf)
    {
        //add the required fonts(required for UTF8 fonts)
        $pdf->AddFont('dejavusans', '', 'DejaVuSans.ttf', true);
        $pdf->AddFont('dejavusans', 'B', 'DejaVuSans-Bold.ttf', true);

        $pdf->SetMargins(20, 20, 20);

        //set default font/colors
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->SetTextColor(200, 10, 10);
        $pdf->SetFillColor(254, 255, 245);

        // add a page
        $pdf->AddPage();
        $pdf->AliasNbPages();

        //disable compression for unit-testing!
        if (isset($_SERVER['ENVIRONMENT']) && 'test' == $_SERVER['ENVIRONMENT']) {
            $pdf->SetCompression(false);
        }

        return $pdf;
    }
}
