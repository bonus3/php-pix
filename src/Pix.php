<?php

namespace PhpPix;

use URLify;
use Endroid\QrCode\QrCode;

class Pix {

    private static $gui = 'BR.GOV.BCB.PIX';

    public static function generateCode($key, $name, $city, $id, $value = 0) {
        $code = self::pfi();
        $code .= self::mai($key);
        $code .= self::mcc();
        $code .= self::currency();
        if (!empty($value)) {
            $code .= self::value($value);
        }
        $code .= self::country();
        $code .= self::name($name);
        $code .= self::city($city);
        $code .= self::transaction($id);
        $crc = self::crc16($code);
        return $code . $crc;
    }

    public static function generateQrCode($key, $name, $city, $id, $value = 0) {
        $code = self::generateCode($key, $name, $city, $id, $value);
        $qrCode = new QrCode($code);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);
        $qrCode->setWriterByName('png');
        $qrCode->setEncoding('UTF-8');
        // Round block sizes to improve readability and make the blocks sharper in pixel based outputs (like png).
        // There are three approaches:
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_MARGIN); // The size of the qr code is shrinked, if necessary, but the size of the final image remains unchanged due to additional margin being added (default)
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_ENLARGE); // The size of the qr code and the final image is enlarged, if necessary
        $qrCode->setRoundBlockSize(true, QrCode::ROUND_BLOCK_SIZE_MODE_SHRINK); // The size of the qr code and the final image is shrinked, if necessary
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
        exit;
    }

    //Payload Format Indicator
    public static function pfi() {
        return '000201';
    }

    //Merchant Account Information
    public static function mai($key) {
        $gui_len = \strlen(self::$gui);
        $key_len = \strlen($key);

        $gui_len_pad = self::lpad($gui_len);
        $key_len_pad = self::lpad($key_len);
        
        $total_len = $gui_len + $key_len + 4 + \strlen($gui_len_pad) + \strlen($key_len_pad);

        return "26{$total_len}00{$gui_len_pad}" . self::$gui . "01{$key_len_pad}{$key}";
    }

    //Merchant Category Code
    public static function mcc() {
        return "52040000";
    }

    //Transaction Currency
    public static function currency() {
        return "5303986";
    }

    public static function value($value) {
        $value = number_format($value, 2, '.', '');
        $value_len_pad = self::lpad(\strlen($value));
        return "54{$value_len_pad}{$value}";
    }

    //Country Code
    public static function country() {
        return "5802BR";
    }

    //Merchant Name
    public static function name($name) {
        $name = strtr(utf8_decode($name), utf8_decode("àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ"), "aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY");
        $name = \substr($name, 0, 25);
        $name = URLify::downcode($name);
        $name_len_pad = self::lpad(\strlen($name));
        return "59{$name_len_pad}$name";
    }

    //Merchant City
    public static function city($city) {
        $city = URLify::downcode($city);
        $city_len_pad = self::lpad(\strlen($city));
        return "60{$city_len_pad}$city";
    }

    //Additional Data Field Template
    //Id transaction
    public static function transaction($id) {
        $id_len = \strlen($id);
        $id_len_pad = self::lpad($id_len);
        $total_len = self::lpad(2 + \strlen($id_len_pad) + $id_len);
        return "62{$total_len}05{$id_len_pad}$id";
    }

    //CRC16
    public static function crc16($input) {
        return "6304" . self::calc_crc($input . "6304");
    }

    private static function lpad($value, $length = 2) {
        return \str_pad($value, $length, '0', STR_PAD_LEFT);
    }

    //https://stackoverflow.com/questions/30035582/how-to-calculate-crc16-ccitt-in-php-hex
    public function calc_crc($str) {
        // The PHP version of the JS str.charCodeAt(i)
        function charCodeAt($str, $i) {
            return ord(substr($str, $i, 1));
        }

        $crc = 0xFFFF;
        $strlen = strlen($str);
        for($c = 0; $c < $strlen; $c++) {
            $crc ^= charCodeAt($str, $c) << 8;
            for($i = 0; $i < 8; $i++) {
                if($crc & 0x8000) {
                    $crc = ($crc << 1) ^ 0x1021;
                } else {
                    $crc = $crc << 1;
                }
            }
        }
        $hex = $crc & 0xFFFF;
        $hex = dechex($hex);
        $hex = strtoupper($hex);

        return $hex;
    }

}
