<?php defined('BASEPATH') or exit('No direct script access allowed');
use Picqer\Barcode;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorHTML;


class Barcode_lib
{
	public function generate_png($value)
	{
		$obj = new BarcodeGeneratorPNG();
        $data = $obj->getBarcode($value, $obj::TYPE_CODE_128);
        return $data;
	}

    public function generate_jpg($value)
	{
		$obj = new BarcodeGeneratorJPG();
        $data = $obj->getBarcode($value, $obj::TYPE_CODE_128);
        return $data;
	}

    public function generate_html($value){
        $obj = new BarcodeGeneratorHTML();
        $data =  $obj->getBarcode($value, $obj::TYPE_CODE_128);
        return $data;
    }

    public function save_png($value)
	{
		$obj = new BarcodeGeneratorPNG();
        file_put_contents('./files/img/barcode/'.$value.'.png', $obj->getBarcode($value, $obj::TYPE_CODE_128, 3, 50));
	}

    public function save_jpg($value)
	{
		$obj = new BarcodeGeneratorJPG();
        file_put_contents('./files/img/barcode/'.$value.'.jpg', $obj->getBarcode($value, $obj::TYPE_CODE_128, 3, 50));
	}
}
