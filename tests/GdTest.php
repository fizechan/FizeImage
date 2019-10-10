<?php

namespace app\controller;

use fize\image\Gd;

/**
 * GD库类
 */
class ControllerFizeImageGd
{

    /**
	 * 测试1
	 */
	public function actionInfo()
    {
        $info = Gd::info();
        var_dump($info);
	}

    public function actionGetSize()
    {
        $gd = new Gd(APP_ROOT . '/../static/test001.png');
        $size = $gd->getSize();
        var_dump($size);
    }

    public function actionGetSizeFromString()
    {
        $img = APP_ROOT . '/../static/test001.png';
        $data       = file_get_contents($img);
        $size_info2 = Gd::getSizeFromString($data);
        var_dump($size_info2);
    }

    public function actionTypeToExtension()
    {
        $ext = Gd::typeToExtension(IMAGETYPE_PNG);
        echo $ext;
    }

    public function actionTypeToMimeType()
    {
        $mime = Gd::typeToMimeType(IMAGETYPE_PNG);
        echo $mime;
    }

    public function actionOutput()
    {
        $gd = new Gd(APP_ROOT . '/../static/test001.png', 'png');
        $gd->output('wbmp');
    }

    public function actionOutput1()
    {
        echo "<img src='index.php?c=FizeImageGd&a=Output'/>";
    }

    public function actionOutput2()
    {
        $gd = new Gd(APP_ROOT . '/../static/test001.png', 'png');
        $gd->output();
    }

    public function actionAffine()
    {
        $gd = new Gd(APP_ROOT . '/../static/test001.png', 'png');
        $affine = [ 1, 1, 1, 1, 1, 1 ];
        $gd->affine($affine);
        $gd->output();
    }

    public function actionArc()
    {
        $img = new Gd();
        $img->create(200, 200);
        $white = $img->colorAllocate(255, 255, 255);
        //$black = $img->colorAllocate( 0, 0, 0);
        $img->arc(100, 100, 150, 150, 0, 360, $white);
        $img->output('png');
    }
}
