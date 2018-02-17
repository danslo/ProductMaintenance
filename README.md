# Product Maintenance for Magento 2

Managing your products from the Magento 2 backend can be a tedious process. This module allows easy maintenance of your Magento 2 products by making it easy to export (single) products to and from CSV files.

You can choose your own product directory, for example a mounted Google Drive folder (using [google-drive-ocamlfuse](https://github.com/astrada/google-drive-ocamlfuse)) from which you can then make quick edits using your favorite editor, like Google Sheets.

In addition, you can just drop product images in an image folder and they will automatically be added to your product.

For importing products, we use [FireGento_FastSimpleImport2](https://github.com/firegento/FireGento_FastSimpleImport2).

## Installation

```bash
$ composer require rubic/magento2-module-product-maintenance
```

## License

Copyright 2018 Rubic

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.