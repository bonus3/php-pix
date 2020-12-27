# PHP Pix
Pix code and QRCode generator

Currently, only generate static qrcode.

To generate QRCode use:

```php
\PhpPix\Pix::generateQrCode(
    'anderson_rockandroll@hotmail.com', //Pix key
    'Anderson da Silva Gonçalves', //Name
    'rio de janeiro', //City
    '123456', //Identifier
    1234.5 //value. Ex.: R$1.234,50 
);
```
This output an PNG image

To generate code copy/paste:
```php
\PhpPix\Pix::generateCode(
    'anderson_rockandroll@hotmail.com', //Pix key
    'Anderson da Silva Gonçalves', //Name
    'rio de janeiro', //City
    '123456', //Identifier
    1234.5 //value. Ex.: R$1.234,50 
);
```

Donate with PIX: anderson_rockandroll@hotmail.com
