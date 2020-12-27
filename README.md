# PHP Pix
Pix code and QRCode generator

Currently, only generate static qrcode.

To generate QRCode use:

```php
\PhpPix\Pix::generateQrCode(
    'anderson_rockandroll@hotmail.com',
    'Anderson da Silva Gonçalves',
    'rio de janeiro',
    '123456',
    1234.5
);
```
This output an PNG image

To generate code copy/paste:
```php
\PhpPix\Pix::generateCode(
    'anderson_rockandroll@hotmail.com',
    'Anderson da Silva Gonçalves',
    'rio de janeiro',
    '123456',
    1234.5
);
```
