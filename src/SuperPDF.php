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
use setasign\Fpdi\Tcpdf\Fpdi as TcpdfFpdi;

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
        $pages = $pdf->setSourceFile($this->file);
        $pdf->Close();

        return $pages;
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
        $cmd .= " -x " . escapeshellarg(($coordinates["x"] ?? 0));
        $cmd .= " -y " . escapeshellarg(($coordinates["y"] ?? 0));
        $cmd .= " -W " . escapeshellarg(($coordinates["w"] ?? 0));
        $cmd .= " -H " . escapeshellarg(($coordinates["h"] ?? 0));
        $cmd .= " -r " . escapeshellarg($dpi);
        $cmd .= " -f " . escapeshellarg($page);
        $cmd .= " -l " . escapeshellarg($page);
        $cmd .= " " . escapeshellarg($this->file);
        $cmd .= " " . escapeshellarg($tmpFile);

        exec($cmd);
        $res = trim(file_get_contents($tmpFile), " \t\n\r\0\x0B\x0C");
        unlink($tmpFile);

        return $res;
    }

    /**
     * @brief Adds the Nth page to the document
     * @param mixed $pdf The Fpdi document
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
    protected function addPage($pdf, int $page): void
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
        $pdf->Close();
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
        $pdf->Close();
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
     * @param mixed $pdf The document
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
     * @brief Saves the document (TCPDF version)
     * @param TcpdfFpdi $pdf The document
     * @param string $file The output file path. If empty, the original file will be overwriten
     * @return void
     * @throws Exception
     */
    protected function saveToWithTcpdf(\setasign\Fpdi\Tcpdf\Fpdi $pdf, string $file): void
    {
        if (empty($file)) {
            $pdf->Output($this->file, "F");
        } else {
            $pdf->Output($file, "F");
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
        $pdf->Close();
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
    protected function addPageWithBackground(\setasign\Fpdi\Fpdi $pdf, string $backgroundPdf, int $page): void
    {
        $pdf->setSourceFile($backgroundPdf);
        $this->addPage($pdf, 1);

        $pdf->setSourceFile($this->file);
        $this->applyTemplate($pdf, $page);
    }

    /**
     * @param Adds a background to the document
     * @param string $backgroundPdf The path to the background PDF file
     * @param int $location The page number or one of the class constants
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
        $pdf->Close();
    }

    /**
     * @brief Prints the text on the current page
     * @param Fpdi $pdf The Fpdi document
     * @param string $text The text to print
     * @param array $params The parameters
     * @return void
     * @throws Exception
     */
    protected function printText(\setasign\Fpdi\Fpdi $pdf, string $text, array $params): void
    {
        $pdf->SetFont(($params["font"] ?? "sans-serif"), ($params["style"] ?? ""));
        $pdf->SetTextColor(($params["color"]["r"] ?? 0), ($params["color"]["g"] ?? 0), ($params["color"]["b"] ?? 0));
        $pdf->SetFontSize(($params["size"] ?? 12));
        $pdf->SetXY(($params["pos"]["x"] ?? 0), ($params["pos"]["y"] ?? 0));
        $pdf->Write(1, $text);
    }

    /**
     * @brief Write text on the document
     * @param string $text The text to write
     * @param array $params An array with parameters
     * @param int $location The page number or one of the class constants
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
    public function writeText(string $text, array $params, int $location, string $output = ""): void
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $sourcePageCount = $pdf->setSourceFile($this->file);

        for ($i = 1; ($i <= $sourcePageCount); $i++) {
            $this->addPage($pdf, $i);

            if ($i == $location) {
                $this->printText($pdf, $text, $params);
            } elseif ($location == self::ON_EACH_PAGE) {
                $this->printText($pdf, $text, $params);
            } elseif ($location == self::ON_ODD_PAGES && $i % 2 == 1) {
                $this->printText($pdf, $text, $params);
            } elseif ($location == self::ON_EVEN_PAGES && $i % 2 == 0) {
                $this->printText($pdf, $text, $params);
            } elseif ($location == self::ON_LAST_PAGE && $i == $sourcePageCount) {
                $this->printText($pdf, $text, $params);
            }
        }

        $this->saveTo($pdf, $output);
        $pdf->Close();
    }

    /**
     * @brief Prints the multicell on the current page
     * @param Fpdi $pdf The Fpdi document
     * @param string $text The text to print
     * @param array $params The parameters
     * @return void
     * @throws Exception
     */
    protected function printMultiCell(\setasign\Fpdi\Fpdi $pdf, string $text, array $params): void
    {
        $pdf->SetFont(($params["font"] ?? "sans-serif"), ($params["style"] ?? ""));
        $pdf->SetTextColor(($params["color"]["r"] ?? 0), ($params["color"]["g"] ?? 0), ($params["color"]["b"] ?? 0));
        $pdf->SetFontSize(($params["size"] ?? 12));
        $pdf->SetXY(($params["pos"]["x"] ?? 0), ($params["pos"]["y"] ?? 0));
        $pdf->MultiCell(
            ($params["cell"]["w"] ?? 0),
            ($params["cell"]["h"] ?? 0),
            $text,
            ($params["cell"]["border"] ?? 0),
            ($params["cell"]["align"] ?? "J"),
            ($params["cell"]["fill"] ?? false)
        );
    }

    /**
     * @brief Write multicell on the document
     * @param string $text The text to write
     * @param array $params An array with parameters
     * @param int $location The page number or one of the class constants
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
    public function writeMultiCellText(string $text, array $params, int $location, string $output = ""): void
    {
        $pdf = new \setasign\Fpdi\Fpdi();
        $sourcePageCount = $pdf->setSourceFile($this->file);

        for ($i = 1; ($i <= $sourcePageCount); $i++) {
            $this->addPage($pdf, $i);

            if ($i == $location) {
                $this->printMultiCell($pdf, $text, $params);
            } elseif ($location == self::ON_EACH_PAGE) {
                $this->printMultiCell($pdf, $text, $params);
            } elseif ($location == self::ON_ODD_PAGES && $i % 2 == 1) {
                $this->printMultiCell($pdf, $text, $params);
            } elseif ($location == self::ON_EVEN_PAGES && $i % 2 == 0) {
                $this->printMultiCell($pdf, $text, $params);
            } elseif ($location == self::ON_LAST_PAGE && $i == $sourcePageCount) {
                $this->printMultiCell($pdf, $text, $params);
            }
        }

        $this->saveTo($pdf, $output);
        $pdf->Close();
    }

    /**
     * @brief Applies a matrix image (GIF, PNG, JPG)
     * @param Fpdi $pdf The Fpdi document
     * @param string $imagePath The path of the image file
     * @param array $params An array of parameters
     * @return void
     * @throws Exception
     */
    protected function applyMatrixImage(\setasign\Fpdi\Fpdi $pdf, string $imagePath, array $params): void
    {
        $pdf->Image($imagePath, ($params["x"] ?? 0), ($params["y"] ?? 0), ($params["w"] ?? 0), ($params["h"] ?? 0));
    }

    /**
     * @brief Applies a vector image (SVG)
     * @param TcpdfFpdi $pdf The Fpdi document
     * @param string $imagePath The path of the image file
     * @param array $params An array of parameters
     * @return void
     * @throws Exception
     */
    protected function applyVectorImage(\setasign\Fpdi\Tcpdf\Fpdi $pdf, string $imagePath, array $params): void
    {
        $pdf->ImageSVG($imagePath, ($params["x"] ?? 0), ($params["y"] ?? 0), ($params["w"] ?? 0), ($params["h"] ?? 0));
    }

    /**
     * @brief Auto selects the appropriate apply function
     * @param mixed $pdf The Fpdi document
     * @param string $imagePath The path of the image file
     * @param array $params An array of parameters
     * @return void
     * @throws Exception
     */
    protected function applyImage($pdf, string $imagePath, array $params): void
    {
        if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) == "svg") {
            $this->applyVectorImage($pdf, $imagePath, $params);
        } else {
            $this->applyMatrixImage($pdf, $imagePath, $params);
        }
    }

    /**
     * @brief Draw an image on the document
     * @param string $imagePath The path of the image file
     * @param array $params An array of parameters
     * @param int $location The page number or one of the class constants
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
    public function drawImage(string $imagePath, array $params, int $location, string $output = ""): void
    {
        if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) == "svg") {
            $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
        } else {
            $pdf = new \setasign\Fpdi\Fpdi();
        }

        $sourcePageCount = $pdf->setSourceFile($this->file);

        for ($i = 1; ($i <= $sourcePageCount); $i++) {
            $this->addPage($pdf, $i);

            if ($i == $location) {
                $this->applyImage($pdf, $imagePath, $params);
            } elseif ($location == self::ON_EACH_PAGE) {
                $this->applyImage($pdf, $imagePath, $params);
            } elseif ($location == self::ON_ODD_PAGES && $i % 2 == 1) {
                $this->applyImage($pdf, $imagePath, $params);
            } elseif ($location == self::ON_EVEN_PAGES && $i % 2 == 0) {
                $this->applyImage($pdf, $imagePath, $params);
            } elseif ($location == self::ON_LAST_PAGE && $i == $sourcePageCount) {
                $this->applyImage($pdf, $imagePath, $params);
            }
        }

        if (strtolower(pathinfo($imagePath, PATHINFO_EXTENSION)) == "svg") {
            $this->saveToWithTcpdf($pdf, $output);
            $pdf->Close();
            $pdf->__destruct(); // I have to do this dirty hack because TCPDF files never destruct by themselves
        } else {
            $this->saveTo($pdf, $output);
            $pdf->Close();
        }
    }

    /**
     * Digitally signs a document
     * @param string $cert The certificate (see openssl_pkcs7_sign)
     * @param string $privateKey The private key (see openssl_pkcs7_sign)
     * @param string $password The private key's password
     * @param string $extraCerts A file with a bunch of extra certificates (see openssl_pkcs7_sign)
     * @param int $certType The type of certification (see TCPDF)
     * @param array $info The infos
     * @param string $approval Enable approval signature
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
    public function sign(
        string $cert = "",
        string $privateKey = "",
        string $password = "",
        string $extraCerts = "",
        int $certType = 2,
        array $info = [],
        string $approval = "",
        string $output = ""
    ): void {
        $pdf = new \setasign\Fpdi\Tcpdf\Fpdi();
        $sourcePageCount = $pdf->setSourceFile($this->file);

        for ($i = 1; ($i <= $sourcePageCount); $i++) {
            $this->addPage($pdf, $i);
        }

        $pdf->setSignature($cert, $privateKey, $password, $extraCerts, $certType, $info, $approval);

        $this->saveToWithTcpdf($pdf, $output);
        $pdf->Close();
        $pdf->__destruct();
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
