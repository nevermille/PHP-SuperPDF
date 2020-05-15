<?php

namespace Lianhua\SuperPDF;

use Exception;
use InvalidArgumentException;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;

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
