# PHP-SuperPDF
A simple library to manipulate PDF files

[![Build Status](https://travis-ci.com/Nevermille/PHP-SuperPDF.svg?branch=master)](https://travis-ci.com/Nevermille/PHP-SuperPDF) [![BCH compliance](https://bettercodehub.com/edge/badge/Nevermille/PHP-SuperPDF?branch=master)](https://bettercodehub.com/) [![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

## Overview

A simple library to manipulate PDF files in PHP

## Compatibility

This library has been tested for PHP 7.3 and higher

## Installation

Just use composer in your project:

```
composer require lianhua/superxml
```

If you don't use composer, clone or download this repository, all you need is inside the src directory, you'll need the FPDI library.

## Usage
### Open a PDF file

You can open a PDF document with a new SuperPDF:

```php
$pdf = new SuperPDF("/path/to/pdf/file");
```

### Count the pages

You can ask for the pages count:

```php
$pagesCount = $pdf->getPageCount();
```

### Extract pages
#### Range

If you want to extract a range of pages into a pdf file:

```php
$pdf->extractPageRange(3, 7, "path/to/pdf/output");
```

#### List

If you want to extract a list of pages into a pdf file:

```php
$pdf->extractPageList([1, 3, 6, 8, 9], "path/to/pdf/output");
```

### Insert a PDF document

If you want to insert a PDF file's contents into the document:

```php
$pdf->insertPages("/path/to/pdf/to/insert", 5); // Inserts the PDF at page 5
$pdf->insertPages("/path/to/pdf/to/insert", SuperPDF::AFTER_EACH_PAGE); // Inserts the PDF after each page
$pdf->insertPages("/path/to/pdf/to/insert", SuperPDF::AFTER_ODD_PAGES); // Inserts the PDF after odd pages
$pdf->insertPages("/path/to/pdf/to/insert", SuperPDF::AFTER_EVEN_PAGES); // Inserts the PDF after even pages
$pdf->insertPages("/path/to/pdf/to/insert", SuperPDF::AT_THE_END); // Inserts the PDF after the last page
```

If you don't want to overwrite the document, you can give an output path:

```php
$pdf->insertPages("/path/to/pdf/to/insert", 5, "/path/to/output");
```

### Add a background

If you want to add a background on your document:

```php
$pdf->addBackground("/path/to/background/pdf", 5); // Adds a background on page 5
$pdf->addBackground("/path/to/background/pdf", SuperPDF::ON_LAST_PAGE); // Adds a background on last page
$pdf->addBackground("/path/to/background/pdf", SuperPDF::ON_ODD_PAGES); // Adds a background on odd pages
$pdf->addBackground("/path/to/background/pdf", SuperPDF::ON_EVEN_PAGES); // Adds a background on even pages
$pdf->addBackground("/path/to/background/pdf", SuperPDF::ON_EACH_PAGE); // Adds a background on each page
```

Like before, if you don't want to overwrite the document, you can give an output path:

```php
$pdf->addBackground("/path/to/background/pdf", 5, "/path/to/output");
```
