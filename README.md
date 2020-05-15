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
