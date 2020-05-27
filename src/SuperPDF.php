<?php

namespace Lianhua\SuperPDF;

use BadMethodCallException;
use Exception;
use InvalidArgumentException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
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
 * @file SuperPDF.php
 * @author Camille Nevermind
 */

/**
 * @brief The main class for PDF manipulation
 * @class SuperPDF
 * @package Lianhua\SuperPDF
 */
class SuperPDF
{
    /**
     * @brief The path to the PDF file
     * @var string
     */
    protected $file;

    public const AT_THE_END = -1;
    public const ON_LAST_PAGE = -1;
    public const AFTER_EACH_PAGE = -2;
    public const ON_EACH_PAGE = -2;
    public const AFTER_ODD_PAGES = -3;
    public const ON_ODD_PAGES = -3;
    public const AFTER_EVEN_PAGES = -4;
    public const ON_EVEN_PAGES = -4;

    /**
     * @brief Returns the number of pages of the PDF document
     * @return int The number of pages
     * @throws InvalidArgumentException
     * @throws PdfParserException
     * @throws PdfTypeException
     */
    public function getPageCount(): int
    {
        $pdf = new Fpdi();
        return $pdf->setSourceFile($this->file);
    }

    /**
     * @brief Extract a text at a rectangle coordinates
     * @param array $coordinates An array of coordinates with "x", "y", "h" and "w" keys
     * @param int $page The page number where to read
     * @param int $dpi The dpi of the PDF rendering
     * @return string The extracted text
     *
     * You need pdftotext in your PATH to use this function
     */
    public function extractText(array $coordinates, int $page = 1, int $dpi = 72): string
    {
        $tmpFile = tempnam(sys_get_temp_dir(), "superpdf");

        // Command line build
        $cmd = "pdftotext";
        $cmd .= " -x " . ($coordinates["x"] ?? 0);
        $cmd .= " -y " . ($coordinates["y"] ?? 0);
        $cmd .= " -W " . ($coordinates["w"] ?? 0);
        $cmd .= " -H " . ($coordinates["h"] ?? 0);
        $cmd .= " -r " . $dpi;
        $cmd .= " -f " . $page;
        $cmd .= " -l " . $page;
        $cmd .= " " . $this->file;
        $cmd .= " " . $tmpFile;

        exec($cmd);
        $res = trim(file_get_contents($tmpFile), " \t\n\r\0\x0B\x0C");
        unlink($tmpFile);

        return $res;
    }

    /**
     * @brief Adds the Nth page to the document
     * @param Fpdi $pdf The Fpdi document
     * @param int $page The page number
     * @return void
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    protected function addPage(\setasign\Fpdi\Fpdi $pdf, int $page): void
    {
        $tplidx = $pdf->importPage($page);
        $format = $pdf->getTemplateSize($tplidx);
        $pdf->AddPage($format["orientation"], [$format["width"], $format["height"]]);
        $pdf->useTemplate($tplidx);
    }

    /**
     * @brief Applies the template to the last page
     * @param Fpdi $pdf The Fpdi document
     * @param int $page The page number
     * @return void
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    protected function applyTemplate(\setasign\Fpdi\Fpdi $pdf, int $page): void
    {
        $tplidx = $pdf->importPage($page);
        $pdf->useTemplate($tplidx);
    }

    /**
     * @brief Extracts a range of pages
     * @param int $first The first page to extract
     * @param int $last The last page to extract
     * @param string $path The output file path
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     */
    public function extractPageRange(int $first, int $last, string $path): void
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->setSourceFile($this->file);

        for ($i = $first; ($i <= $last); $i++) {
            $this->addPage($pdf, $i);
        }

        $pdf->Output("F", $path);
    }

    /**
     * @brief Extracts a list of pages
     * @param array $list The list of page numbers
     * @param string $path The output file path
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     */
    public function extractPageList(array $list, string $path): void
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $pdf->setSourceFile($this->file);

        foreach ($list as $page) {
            $this->addPage($pdf, $page);
        }

        $pdf->Output("F", $path);
    }

    /**
     * @brief Adds another document to a document
     * @param Fpdi $pdf The document
     * @param string $file The PDF file to add
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    protected function insertFile(\setasign\Fpdi\Fpdi $pdf, string $file): void
    {
        $insertPageCount = $pdf->setSourceFile($file);

        for ($i = 1; ($i <= $insertPageCount); $i++) {
            $this->addPage($pdf, $i);
        }

        $pdf->setSourceFile($this->file);
    }

    /**
     * @brief Saves the document
     * @param Fpdi $pdf The document
     * @param string $file The output file path. If empty, the original file will be overwriten
     * @return void
     * @throws Exception
     */
    protected function saveTo(\setasign\Fpdi\Fpdi $pdf, string $file): void
    {
        if (empty($file)) {
            $pdf->Output("F", $this->file);
        } else {
            $pdf->Output("F", $file);
        }
    }

    /**
     * @brief Inserts a PDF content into the document
     * @param string $fileToInsert The PDF file to insert
     * @param int $location The page number where to insert or one of the class constants
     * @param string $output The output file path. If empty, the original file will be overwriten
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     */
    public function insertPages(string $fileToInsert, int $location, string $output = ""): void
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $sourcePageCount = $pdf->setSourceFile($this->file);

        for ($i = 1; ($i <= $sourcePageCount); $i++) {
            if ($i == $location) {
                $this->insertFile($pdf, $fileToInsert);
            }

            $this->addPage($pdf, $i);

            if ($location == self::AFTER_EACH_PAGE) {
                $this->insertFile($pdf, $fileToInsert);
            } elseif ($location == self::AFTER_ODD_PAGES && $i % 2 == 1) {
                $this->insertFile($pdf, $fileToInsert);
            } elseif ($location == self::AFTER_EVEN_PAGES && $i % 2 == 0) {
                $this->insertFile($pdf, $fileToInsert);
            } elseif ($location == self::AT_THE_END && $i == $sourcePageCount) {
                $this->insertFile($pdf, $fileToInsert);
            }
        }

        $this->saveTo($pdf, $output);
    }

    /**
     * @brief Adds a page with a background
     * @param Fpdi $pdf The Fpdi document
     * @param string $backgroundPdf The background PDF file
     * @param int $page The page number where to insert or one of the class constants
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function addPageWithBackground(\setasign\Fpdi\Fpdi $pdf, string $backgroundPdf, int $page): void
    {
        $pdf->setSourceFile($backgroundPdf);
        $this->addPage($pdf, 1);

        $pdf->setSourceFile($this->file);
        $this->applyTemplate($pdf, $page);
    }

    /**
     * @param Adds a background to the document
     * @param string $backgroundPdf The path to the background PDF file
     * @param int $location The page number or one
     * @param string $output The output file path. If empty, the original file will be overwriten
     * @return void
     * @throws PdfParserException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfTypeException
     * @throws PdfReaderException
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws Exception
     */
    public function addBackground(string $backgroundPdf, int $location, string $output = ""): void
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $sourcePageCount = $pdf->setSourceFile($this->file);

        for ($i = 1; ($i <= $sourcePageCount); $i++) {
            if ($i == $location) {
                $this->addPageWithBackground($pdf, $backgroundPdf, $i);
            } elseif ($location == self::ON_EACH_PAGE) {
                $this->addPageWithBackground($pdf, $backgroundPdf, $i);
            } elseif ($location == self::ON_ODD_PAGES && $i % 2 == 1) {
                $this->addPageWithBackground($pdf, $backgroundPdf, $i);
            } elseif ($location == self::ON_EVEN_PAGES && $i % 2 == 0) {
                $this->addPageWithBackground($pdf, $backgroundPdf, $i);
            } elseif ($location == self::ON_LAST_PAGE && $i == $sourcePageCount) {
                $this->addPageWithBackground($pdf, $backgroundPdf, $i);
            } else {
                $this->addPage($pdf, $i);
            }
        }

        $this->saveTo($pdf, $output);
    }

    /**
     * @brief The constructor
     * @param string $filepath The path to the file
     * @return void
     */
    public function __construct(string $filepath)
    {
        if (file_exists($filepath)) {
            $this->file = $filepath;
        } else {
            throw new Exception("File not found : " . $filepath);
        }
    }
}
