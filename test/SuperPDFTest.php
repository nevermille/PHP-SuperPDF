<?php

namespace Lianhua\SuperPDF\Test;

use InvalidArgumentException;
use Lianhua\SuperPDF\SuperPDF as SuperPDFSuperPDF;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use setasign\Fpdi\PdfParser\PdfParserException;

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
class SuperPDF extends TestCase
{
    /**
     * @brief Tests the class instantiation
     * @return void
     * @throws ExpectationFailedException
     */
    public function testInstantiate(): void
    {
        $pdf = new SuperPDFSuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "01.pdf");
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
        $pdf = new SuperPDFSuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "01.pdf");
        $this->assertEquals(1, $pdf->getPageCount());

        $pdf = new SuperPDFSuperPDF(__DIR__ . DIRECTORY_SEPARATOR . "pdf" . DIRECTORY_SEPARATOR . "02.pdf");
        $this->assertEquals(2, $pdf->getPageCount());
    }
}
