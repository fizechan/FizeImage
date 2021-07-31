<?php

namespace fize\image;

/**
 * 图片工具
 */
class Image
{

    /**
     * 仿射变换
     *
     * 参数 `$clip` :
     *   其中键为 "x"，"y"，"width" 和 "height"
     * @param string      $file_name 图像文件路径
     * @param array       $affine    [ a0, b0, a1, b1, a2, b2 ]
     * @param array       $clip      剪切区域
     * @param string|null $to        保存路径，不指定时则覆盖原图片
     * @return bool 成功时返回true，失败时返回false
     */
    public static function affine(string $file_name, array $affine, array $clip = null, string $to = null): bool
    {
        if (is_null($to)) {
            $to = $file_name;
        }
        $img = new Gd($file_name);
        $img->affine($affine, $clip);
        return $img->output(null, $to);
    }

    /**
     * 裁剪图像
     *
     * 参数 `$rect` :
     *   其中键为 "x"，"y"，"width" 和 "height"
     * @param string      $file_name 图像文件路径
     * @param array       $rect      裁剪区域
     * @param string|null $to        保存路径，不指定时则覆盖原图片
     * @return bool 成功时返回true，失败时返回false
     */
    public static function crop(string $file_name, array $rect, string $to = null): bool
    {
        if (is_null($to)) {
            $to = $file_name;
        }
        $img = new Gd($file_name);
        $img->crop($rect);
        return $img->output(null, $to, ['quality' => 100]);
    }

    /**
     * 用给定角度旋转图像
     * @param string      $file_name          图像文件路径
     * @param float       $angle              角度
     * @param int         $bgd_color          指定旋转后未覆盖区域的颜色
     * @param int         $ignore_transparent 如果被设为非零值，则透明色会被忽略。
     * @param string|null $to                 保存路径，不指定时则覆盖原图片
     * @return bool 成功时返回true，失败时返回false
     */
    public static function rotate(string $file_name, float $angle, int $bgd_color = 0, int $ignore_transparent = 0, string $to = null): bool
    {
        if (is_null($to)) {
            $to = $file_name;
        }
        $img = new Gd($file_name);
        $img->rotate($angle, $bgd_color, $ignore_transparent);
        return $img->output(null, $to);
    }

    /**
     * 使用给定的新宽度和高度缩放图像
     * @param string      $file_name  图像文件路径
     * @param int         $new_width  新宽度
     * @param int         $new_height 新高度，-1表示自动计算
     * @param int         $mode       模式
     * @param string|null $to         保存路径，不指定时则覆盖原图片
     * @return bool 成功时返回true，失败时返回false
     */
    public static function scale(string $file_name, int $new_width, int $new_height = -1, int $mode = 3, string $to = null): bool
    {
        if ($new_width == -1 && $new_height == -1) {
            return false;
        }
        if (is_null($to)) {
            $to = $file_name;
        }
        $img = new Gd($file_name);
        list($dst_w, $dst_h) = $img->getSize();
        if ($new_width == -1) {
            $new_width = (int)round($new_height * $dst_w / $dst_h);
        }
        $img->scale($new_width, $new_height, $mode);
        return $img->output(null, $to);
    }

    /**
     * 使用给定模式翻转图像
     * @param string      $file_name 图像文件路径
     * @param int         $mode      常量IMG_FLIP_*
     * @param string|null $to        保存路径，不指定时则覆盖原图片
     * @return bool 成功时返回true，失败时返回false
     */
    public static function flip(string $file_name, int $mode, string $to = null): bool
    {
        if (is_null($to)) {
            $to = $file_name;
        }
        $img = new Gd($file_name);
        $img->flip($mode);
        return $img->output(null, $to);
    }

    /**
     * 添加图片水印
     *
     * 参数 `$coord` :
     *   支持键名[left、right、top、bottom]
     * @param string      $file_name 图像文件路径
     * @param string      $source    水印文件路径
     * @param array       $coord     水印坐标
     * @param int         $alpha     透明度
     * @param string|null $to        保存路径，不指定时则覆盖原图片
     * @return bool
     */
    public static function water(string $file_name, string $source, array $coord, int $alpha = 100, string $to = null): bool
    {
        if (is_null($to)) {
            $to = $file_name;
        }
        $img = new Gd($file_name);
        list($dst_w, $dst_h) = $img->getSize();
        $water = new Gd($source);
        list($src_w, $src_h) = $water->getSize();

        $dst_x = 0;
        if (isset($coord['left'])) {
            $dst_x = $coord['left'];
        }
        if (isset($coord['right'])) {
            $dst_x = $dst_w - $src_w - $coord['right'];
        }
        if (isset($coord['left']) && isset($coord['right']) && $coord['left'] == 0 && $coord['right'] == 0) {
            $dst_x = (int)round($dst_w / 2 - $src_w / 2);
        }

        $dst_y = 0;
        if (isset($coord['top'])) {
            $dst_y = $coord['top'];
        }
        if (isset($coord['bottom'])) {
            $dst_y = $dst_h - $src_h - $coord['bottom'];
        }
        if (isset($coord['top']) && isset($coord['bottom']) && $coord['top'] == 0 && $coord['bottom'] == 0) {
            $dst_y = (int)round($dst_h / 2 - $src_h / 2);
        }

        $img->copyMerge($source, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $alpha);
        return $img->output(null, $to);
    }

    /**
     * 添加文字水印
     *
     * 参数 `$coord` :
     *   支持键名[left、right、top、bottom]
     * @param string      $file_name 图像文件路径
     * @param array       $coord     水印坐标
     * @param string      $text      水印文字
     * @param string      $font      字体文件路径
     * @param int         $size      字体大小
     * @param string      $color     RGBA颜色值
     * @param int         $angle     角度
     * @param string|null $to        保存路径，不指定时则覆盖原图片
     * @return bool
     */
    public static function text(string $file_name, array $coord, string $text, string $font, int $size, string $color = '#00000000', int $angle = 0, string $to = null): bool
    {
        $box = Gd::ttfbbox($size, $angle, $font, $text);
        $minx = min($box[0], $box[2], $box[4], $box[6]);
        $maxx = max($box[0], $box[2], $box[4], $box[6]);
        $miny = min($box[1], $box[3], $box[5], $box[7]);
        $maxy = max($box[1], $box[3], $box[5], $box[7]);
        $src_w = $maxx - $minx;
        $src_h = $maxy - $miny;

        $img = new Gd($file_name);
        list($dst_w, $dst_h) = $img->getSize();

        $dst_x = 0;
        if ($minx < 0) {
            $dst_x += abs($minx);
        } else {
            $dst_x -= $minx;
        }
        if (isset($coord['left']) && isset($coord['right']) && $coord['left'] == 0 && $coord['right'] == 0) {
            $dst_x += (int)round($dst_w / 2 - $src_w / 2);
        } else {
            if (isset($coord['left']) && !isset($coord['right'])) {
                $dst_x += $coord['left'];
            } elseif (isset($coord['right']) && !isset($coord['left'])) {
                $dst_x += $dst_w - $src_w - $coord['right'];
            }
        }

        $dst_y = 0;
        if ($miny < 0) {
            $dst_y += abs($miny);
        } else {
            $dst_y -= $miny;
        }

        if (isset($coord['top']) && isset($coord['bottom']) && $coord['top'] == 0 && $coord['bottom'] == 0) {
            $dst_y += (int)round($dst_h / 2 - $src_h / 2);
        } else {
            if (isset($coord['top']) && !isset($coord['bottom'])) {
                $dst_y += $coord['top'];
            }
            if (isset($coord['bottom']) && !isset($coord['top'])) {
                $dst_y += $dst_h - $src_h - $coord['bottom'];
            }
        }

        $color = str_split(substr($color, 1), 2);
        $color = array_map('hexdec', $color);
        if (empty($color[3]) || $color[3] > 127) {
            $color[3] = 0;
        }
        $color = $img->colorallocatealpha($color[0], $color[1], $color[2], $color[3]);

        $img->ttftext($size, $angle, $dst_x, $dst_y, $color, $font, $text);
        return $img->output(null, $to);
    }
}
