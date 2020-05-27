<?php

namespace Lianhua\SuperPDF\Test;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use Lianhua\SuperPDF\SuperPDF;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use SebastianBergmann\RecursionContext\InvalidArgumentException as RecursionContextInvalidArgumentException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;

/*
SuperPDF Library
Copyright (C) 2020  Lianhua Studio

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * @file SuperPDFTest.php
 * @author Camille Nevermind
 */

/**
 * @brief The tests for SuperPDF
 * @class SuperPDFTest
 * @package Lianhua\SuperPDF\Test
 */
class SuperPDFTest extends TestCase
{
    /**
     * @brief Tests the class instantiation
     * @return void
     * @throws ExpectationFailedException
     */
    public function testInstantiate(): void
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "01.pdf");
        $this->assertNotNull($pdf);
    }

    /**
     * @brief Tests the page count function
     * @return void
     * @throws InvalidArgumentException
     * @throws PdfParserException
     * @throws ExpectationFailedException
     */
    public function testPageCount(): void
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "01.pdf");
        $this->assertEquals(1, $pdf->getPageCount());

        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf");
        $this->assertEquals(2, $pdf->getPageCount());
    }

    /**
     * @brief Tests the extract text function
     * @return void
     * @throws ExpectationFailedException
     */
    public function testExtractText()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "01.pdf");
        $this->assertEquals("TEST", $pdf->extractText(["x" => 176, "y" => 257, "w" => 101, "h" => 40]));
        $this->assertEquals("LOREM", $pdf->extractText(["x" => 423, "y" => 115, "w" => 40, "h" => 14]));
        $this->assertEquals("IPSUM", $pdf->extractText(["x" => 435, "y" => 353, "w" => 64, "h" => 20]));
        $this->assertEquals("SIT", $pdf->extractText(["x" => 69, "y" => 485, "w" => 32, "h" => 20]));
        $this->assertEquals("DOLOR", $pdf->extractText(["x" => 365, "y" => 468, "w" => 143, "h" => 38]));
        $this->assertEquals("AMET", $pdf->extractText(["x" => 265, "y" => 600, "w" => 27, "h" => 11]));

        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf");
        $this->assertEquals("PAGE 1", $pdf->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13]));
        $this->assertEquals("PAGE 2", $pdf->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 2));
    }

    /**
     * @brief Tests the range extract function
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testExtractPageRange()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";
        $pdf->extractPageRange(3, 7, $out);

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("4", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 2));
        $this->assertEquals("5", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 3));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 4));
        $this->assertEquals("7", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 5));
    }

    /**
     * @brief Tests the list extract function
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testExtractPageList()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";
        $pdf->extractPageList([1, 3, 6, 8, 9], $out);

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("1", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 2));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 3));
        $this->assertEquals("8", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 4));
        $this->assertEquals("9", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 5));
    }

    /**
     * @brief Tests the insert function with a page number
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testInsertAtPageNumber()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";
        $pdf->insertPages(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf", 5, $out);

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("1", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("2", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 2));
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 3));
        $this->assertEquals("4", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 4));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 5));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 6));
        $this->assertEquals("5", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 7));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 8));
        $this->assertEquals("7", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 9));
        $this->assertEquals("8", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 10));
        $this->assertEquals("9", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 11));
    }

    /**
     * @brief Tests the insert function at the end
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testInsertAtTheEnd()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";

        $pdf->insertPages(
            __DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf",
            SuperPDF::AT_THE_END,
            $out
        );

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("1", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("2", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 2));
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 3));
        $this->assertEquals("4", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 4));
        $this->assertEquals("5", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 5));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 6));
        $this->assertEquals("7", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 7));
        $this->assertEquals("8", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 8));
        $this->assertEquals("9", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 9));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 10));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 11));
    }

    /**
     * @brief Tests the insert function after odd pages
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testInsertAfterOddPages()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";

        $pdf->insertPages(
            __DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf",
            SuperPDF::AFTER_ODD_PAGES,
            $out
        );

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("1", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 2));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 3));
        $this->assertEquals("2", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 4));
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 5));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 6));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 7));
        $this->assertEquals("4", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 8));
        $this->assertEquals("5", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 9));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 10));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 11));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 12));
        $this->assertEquals("7", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 13));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 14));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 15));
        $this->assertEquals("8", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 16));
        $this->assertEquals("9", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 17));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 18));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 19));
    }

    /**
     * @brief Tests the insert function after even pages
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testInsertAfterEvenPages()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";

        $pdf->insertPages(
            __DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf",
            SuperPDF::AFTER_EVEN_PAGES,
            $out
        );

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("1", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("2", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 2));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 3));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 4));
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 5));
        $this->assertEquals("4", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 6));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 7));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 8));
        $this->assertEquals("5", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 9));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 10));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 11));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 12));
        $this->assertEquals("7", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 13));
        $this->assertEquals("8", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 14));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 15));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 16));
        $this->assertEquals("9", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 17));
    }

    /**
     * @brief Tests the insert function after each pages
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws RecursionContextInvalidArgumentException
     */
    public function testInsertAfterEachPage()
    {
        $pdf = new SuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "04.pdf");
        $out = tempnam(sys_get_temp_dir(), "PDF") . ".pdf";

        $pdf->insertPages(
            __DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf",
            SuperPDF::AFTER_EACH_PAGE,
            $out
        );

        $pdfOut = new SuperPDF($out);
        $this->assertEquals("1", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 1));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 2));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 3));
        $this->assertEquals("2", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 4));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 5));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 6));
        $this->assertEquals("3", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 7));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 8));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 9));
        $this->assertEquals("4", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 10));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 11));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 12));
        $this->assertEquals("5", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 13));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 14));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 15));
        $this->assertEquals("6", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 16));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 17));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 18));
        $this->assertEquals("7", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 19));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 20));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 21));
        $this->assertEquals("8", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 22));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 23));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 24));
        $this->assertEquals("9", $pdfOut->extractText(["x" => 67, "y" => 70, "w" => 18, "h" => 18], 25));
        $this->assertEquals("PAGE 1", $pdfOut->extractText(["x" => 175, "y" => 307, "w" => 39, "h" => 13], 26));
        $this->assertEquals("PAGE 2", $pdfOut->extractText(["x" => 278, "y" => 424, "w" => 42, "h" => 13], 27));
    }
}
