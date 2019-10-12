<?php
/** @noinspection PhpComposerExtensionStubsInspection */


use fize\image\Image;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{

    public function testAffine()
    {
        $img = __DIR__ . '/data/image1.jpg';
        $affine = [1, 0, 0, 1, 0, 0];
        $out = __DIR__ . '/output/affine1.jpg';
        $result = Image::affine($img, $affine, null, $out);
        self::assertTrue($result);
    }

    public function testCrop()
    {
        $img = __DIR__ . '/data/image2.jpg';
        $rect = [
            'x'      => 100,
            'y'      => 100,
            'width'  => 300,
            'height' => 600
        ];
        $out = __DIR__ . '/output/crop2.jpg';
        $result = Image::crop($img, $rect, $out);
        self::assertTrue($result);
    }

    public function testRotate()
    {
        $img = __DIR__ . '/data/image2.jpg';
        $out = __DIR__ . '/output/rotate2.jpg';
        $result = Image::rotate($img, 90, 0, 0, $out);
        self::assertTrue($result);
    }

    public function testScale()
    {
        $img = __DIR__ . '/data/image2.jpg';
        $out1 = __DIR__ . '/output/scale1.jpg';
        $result1 = Image::scale($img, 600, -1, 3, $out1);
        self::assertTrue($result1);
        $out2 = __DIR__ . '/output/scale2.jpg';
        $result2 = Image::scale($img, -1, 600, 3, $out2);
        self::assertTrue($result2);
    }

    public function testFlip()
    {
        $img = __DIR__ . '/data/image2.jpg';
        $out1 = __DIR__ . '/output/flip1.jpg';
        $result1 = Image::flip($img, IMG_FLIP_HORIZONTAL, $out1);
        self::assertTrue($result1);
        $out2 = __DIR__ . '/output/flip2.jpg';
        $result2 = Image::flip($img, IMG_FLIP_VERTICAL, $out2);
        self::assertTrue($result2);
        $out3 = __DIR__ . '/output/flip3.jpg';
        $result3 = Image::flip($img, IMG_FLIP_BOTH, $out3);
        self::assertTrue($result3);
    }

    public function testWater()
    {
        $img = __DIR__ . '/data/image1.jpg';
        $water = __DIR__ . '/data/water.png';

        $out1 = __DIR__ . '/output/water1.jpg';
        $coord1 = [
            'top'  => 0,
            'left' => 0
        ];  //左上
        $result1 = Image::water($img, $water, $coord1, 100, $out1);
        self::assertTrue($result1);

        $out2 = __DIR__ . '/output/water2.jpg';
        $coord2 = [
            'bottom' => 0,
            'left'   => 0
        ];  //左下
        $result2 = Image::water($img, $water, $coord2, 100, $out2);
        self::assertTrue($result2);

        $out3 = __DIR__ . '/output/water3.jpg';
        $coord3 = [
            'top'   => 0,
            'right' => 0
        ];  //右上
        $result3 = Image::water($img, $water, $coord3, 100, $out3);
        self::assertTrue($result3);

        $out4 = __DIR__ . '/output/water4.jpg';
        $coord4 = [
            'bottom' => 0,
            'right'  => 0
        ];  //右下
        $result4 = Image::water($img, $water, $coord4, 100, $out4);
        self::assertTrue($result4);

        $out5 = __DIR__ . '/output/water5.jpg';
        $coord5 = [
            'top'    => 0,
            'bottom' => 0,
            'left'   => 0,
            'right'  => 0
        ];  //居中
        $result5 = Image::water($img, $water, $coord5, 50, $out5);
        self::assertTrue($result5);

    }

    public function testText()
    {
        $img = __DIR__ . '/data/image2.jpg';
        $water_str = '测试专用';
        $font = __DIR__ . '/data/msyh.ttc';

        $out1 = __DIR__ . '/output/text1.jpg';
        $coord1 = [
            'top'  => 0,
            'left' => 0
        ];  //左上
        $result1 = Image::text($img, $coord1, $water_str, $font, 50, '#00000000', 0, $out1);
        self::assertTrue($result1);

        $out2 = __DIR__ . '/output/text2.jpg';
        $coord2 = [
            'bottom' => 0,
            'left'   => 0
        ];  //左下
        $result2 = Image::text($img, $coord2, $water_str, $font, 20, '#FFFFFFFF', 45, $out2);
        self::assertTrue($result2);

        $out3 = __DIR__ . '/output/text3.jpg';
        $coord3 = [
            'top'   => 0,
            'right' => 0
        ];  //右上
        $result3 = Image::text($img, $coord3, $water_str, $font, 24, '#FFFFFFFF', 90, $out3);
        self::assertTrue($result3);

        $out4 = __DIR__ . '/output/text4.jpg';
        $coord4 = [
            'bottom' => 0,
            'right'  => 0
        ];  //右下
        $result4 = Image::text($img, $coord4, $water_str, $font, 20, '#FFFFFFFF', 135, $out4);
        self::assertTrue($result4);

        $out5 = __DIR__ . '/output/text5.jpg';
        $coord5 = [
            'top'    => 0,
            'bottom' => 0,
            'left'   => 0,
            'right'  => 0
        ];  //居中
        $result5 = Image::text($img, $coord5, $water_str, $font, 20, '#FFFFFFFF', 180, $out5);
        self::assertTrue($result5);
    }


}
