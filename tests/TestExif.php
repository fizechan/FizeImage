<?php


use fize\image\Exif;
use PHPUnit\Framework\TestCase;

class TestExif extends TestCase
{

    public function test__construct()
    {
        $file = __DIR__ . '/data/image1.jpg';
        $exif = new Exif($file);
        var_dump($exif);
        self::assertIsObject($exif);
    }

    public function testImagetype()
    {
        $file = __DIR__ . '/data/image1.jpg';
        $exif = new Exif($file);
        $type = $exif->imagetype();
        var_dump($type);
        self::assertEquals(IMAGETYPE_JPEG, $type);
    }

    public function testReadData()
    {
        $file = __DIR__ . '/data/image1.jpg';
        $exif = new Exif($file);
        $data = $exif->readData();
        var_dump($data);
        self::assertIsArray($data);

        $data_file = $exif->readData('FILE');
        var_dump($data_file);
        self::assertIsArray($data_file);

        $data_computed = $exif->readData('COMPUTED');
        var_dump($data_computed);
        self::assertIsArray($data_computed);
    }

    public function testTagname()
    {
        $value = Exif::tagname(256);
        var_dump($value);
        self::assertEquals('ImageWidth', $value);
    }

    public function testThumbnail()
    {
        $file = __DIR__ . '/data/image1.jpg';
        $exif = new Exif($file);
        $image = $exif->thumbnail($width, $height, $imagetype);
        self::assertNotFalse($image);
        var_dump($width);
        var_dump($height);
        var_dump($imagetype);
    }
}
