<?php

use fize\image\Gd;
use PHPUnit\Framework\TestCase;


class TestGd extends TestCase
{

    public function testInfo()
    {
        $info = Gd::info();
        var_dump($info);
        self::assertIsArray($info);
    }

    public function testGetSize()
    {
        $gd = new Gd(__DIR__ . '/data/image1.jpg');
        $size = $gd->getSize();
        var_dump($size);
        self::assertIsArray($size);
    }

    public function testGetSizeFromString()
    {
        $img = __DIR__ . '/data/image1.jpg';
        $data = file_get_contents($img);
        $size_info2 = Gd::getSizeFromString($data);
        var_dump($size_info2);
        self::assertIsArray($size_info2);
    }

    public function testTypeToExtension()
    {
        $ext = Gd::typeToExtension(IMAGETYPE_PNG);
        echo $ext;
        self::assertIsString($ext);
    }

    public function testTypeToMimeType()
    {
        $mime = Gd::typeToMimeType(IMAGETYPE_PNG);
        echo $mime;
        self::assertIsString($mime);
    }

    public function testOutput()
    {
        $gd = new Gd(__DIR__ . '/data/image1.jpg', 'jpeg');
        $gd->output('wbmp', __DIR__ . '/output/image1.wbmp');
        self::assertFileExists(__DIR__ . '/output/image1.wbmp');
    }

    public function testAffine()
    {
        $gd = new Gd(__DIR__ . '/data/image1.jpg', 'jpeg');
        $affine = [1, 0, 0, 1, 0, 0];
        $gd->affine($affine);
        $gd->output('jpeg', __DIR__ . '/output/image1.jpg');
        self::assertFileExists(__DIR__ . '/output/image1.jpg');
    }

    public function testArc()
    {
        $options = [
            'width'     => 300,
            'height'    => 400,
            'truecolor' => true
        ];
        $img = new Gd(null, null, $options);
        $rst = $img->create(200, 200);
        self::assertIsResource($rst);
        $white = $img->colorAllocate(255, 255, 255);
        //$black = $img->colorAllocate( 0, 0, 0);
        $img->arc(100, 100, 150, 150, 0, 360, $white);
        $img->output('png', __DIR__ . '/output/image1.png');
        self::assertFileExists(__DIR__ . '/output/image1.png');
    }
}
