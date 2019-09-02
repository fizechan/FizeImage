<?php

namespace fize\image;


/**
 * 图片工具类
 * @package fize\image
 */
class Image
{

    /**
     * 仿射变换
     * @param string $file_name 图像文件路径
     * @param array $affine [ a0, b0, a1, b1, a2, b2 ]
     * @param array $clip 剪切区域,其中键为 "x"，"y"，"width" 和 "height"
     * @param string $to 保存路径，不指定时则覆盖原图片
     */
    public static function affine($file_name, array $affine, array $clip = null, $to = null)
    {
        $img = new Gd($file_name);
        $img->affine($affine, $clip);
        $img->output(null, $to);
    }

    /**
     * 裁剪图像
     * @param string $file_name 图像文件路径
     * @param array $rect [x, y, width, height]
     * @param string $to 保存路径，不指定时则覆盖原图片
     */
    public static function crop($file_name, array $rect, $to = null)
    {
        $img = new Gd($file_name);
        $img->crop($rect);
        $img->output(null, $to);
    }

    /**
     * 用给定角度旋转图像
     * @param string $file_name 图像文件路径
     * @param float $angle 角度
     * @param int $bgd_color 指定旋转后未覆盖区域的颜色
     * @param int $ignore_transparent 如果被设为非零值，则透明色会被忽略（否则会被保留）。
     * @param string $to 保存路径，不指定时则覆盖原图片
     */
    public static function rotate($file_name, $angle, $bgd_color, $ignore_transparent = 0, $to = null)
    {
        $img = new Gd($file_name);
        $img->rotate($angle, $bgd_color, $ignore_transparent);
        $img->output(null, $to);
    }

    /**
     * 使用给定的新宽度和高度缩放图像
     * @param string $file_name 图像文件路径
     * @param int $new_width 新宽度
     * @param int $new_height 新高度，-1表示自动计算
     * @param int $mode 模式
     * @param string $to 保存路径，不指定时则覆盖原图片
     */
    public static function scale($file_name, $new_width, $new_height = -1, $mode = 3, $to = null)
    {
        $img = new Gd($file_name);
        $img->scale($new_width, $new_height, $mode);
        $img->output(null, $to);
    }
}
