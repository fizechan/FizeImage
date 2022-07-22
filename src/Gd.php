<?php

namespace Fize\Image;

/**
 * 图片GD底层类
 */
class Gd
{

    /**
     * @var string 文件路径
     */
    private $file;

    /**
     * @var resource 图像资源
     */
    private $resource;

    /**
     * 初始化
     *
     * 参数 `$options` :
     *   $filename为null时，该参数必须指定
     * @param string|null $filename 指定图片路径，为null表示不指定
     * @param string|null $from     从指定资源创建
     * @param array       $options  额外选项
     */
    public function __construct(string $filename = null, string $from = null, array $options = [])
    {
        $this->file = $filename;
        if ($filename && is_null($from)) {
            $from = $this->getType();
        }
        if ($from) {
            $this->resource = $this->createFrom($filename, $from, $options);
        } else {
            if (isset($options['width']) && isset($options['height'])) {
                $width = $options['width'];
                $height = $options['height'];
                $truecolor = $options['truecolor'] ?? true;
                $this->resource = $this->create($width, $height, $truecolor);
            }
        }
    }

    /**
     * 析构，释放内存
     */
    public function __destruct()
    {
        if ($this->resource) {
            $this->destroy($this->resource);
            $this->resource = null;
        }
    }

    /**
     * 取得当前安装的 GD 库的信息
     * @return array
     */
    public static function info(): array
    {
        return gd_info();
    }

    /**
     * 取得图像大小
     * @param array $imageinfo
     * @return array
     */
    public function getSize(array &$imageinfo = null): array
    {
        return getimagesize($this->file, $imageinfo);
    }

    /**
     * 获取图片真实后缀
     * @return string
     */
    public function getType(): string
    {
        $info = $this->getSize();
        $type_tag = $info[2];
        $tags = [
            '', 'gif', 'jpg', 'png', 'swf', 'psd', 'bmp', 'tiff', 'tiff', 'jpc',
            'jp2', 'jpx', 'jb2', 'swc', 'iff', 'wbmp', 'xbm'
        ];
        return $tags[$type_tag];
    }

    /**
     * 从字符串中获取图像尺寸信息
     * @param string $imagedata
     * @param array  $imageinfo
     * @return array
     */
    public static function getSizeFromString(string $imagedata, array &$imageinfo = null): array
    {
        return getimagesizefromstring($imagedata, $imageinfo);
    }

    /**
     * 取得图像类型的文件后缀
     * @param int  $imagetype   IMAGETYPE_XXX 系列常量之一。
     * @param bool $include_dot 是否在后缀名前加一个点。
     * @return string
     */
    public static function typeToExtension(int $imagetype, bool $include_dot = false): string
    {
        return image_type_to_extension($imagetype, $include_dot);
    }

    /**
     * 返回的图像类型的 MIME 类型
     * @param int $imagetype IMAGETYPE_XXX 系列常量之一。
     * @return string
     */
    public static function typeToMimeType(int $imagetype): string
    {
        return image_type_to_mime_type($imagetype);
    }

    /**
     * 输出图像
     * @param string|null $type     输出类型
     * @param string|null $filename 指定输出文件路径，不指定则直接在浏览器显示
     * @param array       $options  可选的参数
     * @return bool 如果是直接显示图像则返回null
     */
    public function output(string $type = null, string $filename = null, array $options = [])
    {
        if (is_null($type)) {
            $type = $this->getType();
        }
        switch (strtolower($type)) {
            case 'bmp':
                $compressed = $options['compressed'] ?? true;
                if ($filename) {
                    return imagebmp($this->resource, $filename, $compressed);
                } else {
                    header('Content-type: ' . self::typeToMimeType(IMAGETYPE_BMP));
                    imagebmp($this->resource, null, $compressed);
                }
                break;
            case 'gd':
                if ($filename) {
                    return imagegd($this->resource, $filename);
                } else {
                    imagegd($this->resource);
                }
                break;
            case 'gd2':
                $chunk_size = $options['chunk_size'] ?? null;
                $type = $options['type'] ?? null;
                if ($filename) {
                    return imagegd2($this->resource, $filename, $chunk_size, $type);
                } else {
                    imagegd2($this->resource, null, $chunk_size, $type);
                }
                break;
            case 'gif':
                if ($filename) {
                    return imagegif($this->resource, $filename);
                } else {
                    header('Content-type: ' . self::typeToMimeType(IMAGETYPE_GIF));
                    imagegif($this->resource);
                }
                break;
            case 'jpg':
            case 'jpeg':
                $quality = $options['quality'] ?? -1;
                if ($filename) {
                    return imagejpeg($this->resource, $filename, $quality);
                } else {
                    header('Content-type: ' . self::typeToMimeType(IMAGETYPE_JPEG));
                    imagejpeg($this->resource, null, $quality);
                }
                break;
            case 'png':
                $quality = $options['quality'] ?? null;
                $filters = $options['filters'] ?? null;
                if ($filename) {
                    return imagepng($this->resource, $filename, $quality, $filters);
                } else {
                    header('Content-type: ' . self::typeToMimeType(IMAGETYPE_PNG));
                    imagepng($this->resource, null, $quality, $filters);
                }
                break;
            case 'wbmp':
                if (function_exists('imagewbmp')) {
                    $foreground = $options['foreground'] ?? null;
                    if ($filename) {
                        return imagewbmp($this->resource, $filename, $foreground);
                    } else {
                        header('Content-type: ' . self::typeToMimeType(IMAGETYPE_WBMP));
                        imagewbmp($this->resource, null, $foreground);
                    }
                } elseif (function_exists('image2wbmp')) {
                    $threshold = $options['threshold'] ?? null;
                    if ($filename) {
                        return image2wbmp($this->resource, $filename, $threshold);
                    } else {
                        header('Content-type: ' . self::typeToMimeType(IMAGETYPE_WBMP));
                        image2wbmp($this->resource, null, $threshold);
                    }
                }
                break;
            case 'webp':
                $quality = $options['quality'] ?? 80;
                if ($filename) {
                    return imagewebp($this->resource, $filename, $quality);
                } else {
                    header('Content-type: ' . self::typeToMimeType(IMAGETYPE_WEBP));
                    imagewebp($this->resource, null, $quality);
                }
                break;
            case 'xbm':
                $foreground = $options['foreground'] ?? null;
                if ($filename) {
                    return imagexbm($this->resource, $filename, $foreground);
                } else {
                    header('Content-type: ' . self::typeToMimeType(IMAGETYPE_XBM));
                    imagexbm($this->resource, null, $foreground);
                }
                break;
        }
        return null;
    }

    /**
     * 仿射变换
     *
     * 参数 `$clip` :
     *   其中键为 "x"，"y"，"width" 和 "height"
     * @param array $affine [ a0, b0, a1, b1, a2, b2 ]
     * @param array $clip   剪切区域
     * @return resource 失败时返回false
     */
    public function affine(array $affine, array $clip = null)
    {
        if ($clip) {
            $resource = imageaffine($this->resource, $affine, $clip);
        } else {
            $resource = imageaffine($this->resource, $affine);
        }
        $this->resource = $resource;
        return $resource;
    }

    /**
     * 连接两个仿射变换矩阵
     * @param array $m1 仿射变换矩阵1
     * @param array $m2 仿射变换矩阵2
     * @return array 失败返回false
     */
    public static function affineMatrixConcat(array $m1, array $m2): array
    {
        return imageaffinematrixconcat($m1, $m2);
    }

    /**
     * 得到一个仿射变换矩阵
     * @param int   $type    常量IMG_AFFINE_*
     * @param mixed $options 其他选项
     * @return array 失败返回false
     */
    public static function affineMatrixGet(int $type, $options = null): array
    {
        return imageaffinematrixget($type, $options);
    }

    /**
     * 设定图像的混色模式
     * @param bool $blendmode 启用或禁用
     * @return bool
     */
    public function alphaBlending(bool $blendmode): bool
    {
        return imagealphablending($this->resource, $blendmode);
    }

    /**
     * 是否使用抗锯齿功能
     * @param bool $enabled 启用或禁用
     * @return bool
     */
    public function antialias(bool $enabled): bool
    {
        return imageantialias($this->resource, $enabled);
    }

    /**
     * 画椭圆弧
     *
     * 参数 `$start` :
     *   0°位于三点钟位置，以顺时针方向绘画。
     * 参数 `$end` :
     *   0°位于三点钟位置，以顺时针方向绘画。
     * @param int $cx     中心点x轴坐标
     * @param int $cy     中心点y轴坐标
     * @param int $width  椭圆宽度
     * @param int $height 椭圆高度
     * @param int $start  起点角度
     * @param int $end    结束点角度
     * @param int $color  配色识符
     * @return bool
     */
    public function arc(int $cx, int $cy, int $width, int $height, int $start, int $end, int $color): bool
    {
        return imagearc($this->resource, $cx, $cy, $width, $height, $start, $end, $color);
    }

    /**
     * 水平地画一个字符
     * @param int    $font  更大的数字对应于更大的字体
     * @param int    $x     左上角x轴坐标
     * @param int    $y     左上角y轴坐标
     * @param string $c     字符串
     * @param int    $color 配色识符
     * @return bool
     */
    public function char(int $font, int $x, int $y, string $c, int $color): bool
    {
        return imagechar($this->resource, $font, $x, $y, $c, $color);
    }

    /**
     * 垂直地画一个字符
     * @param int    $font  更大的数字对应于更大的字体
     * @param int    $x     左上角x轴坐标
     * @param int    $y     左上角y轴坐标
     * @param string $c     字符串
     * @param int    $color 配色识符
     * @return bool
     */
    public function charUp(int $font, int $x, int $y, string $c, int $color): bool
    {
        return imagecharup($this->resource, $font, $x, $y, $c, $color);
    }

    /**
     * 分配颜色
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @return int 成功返回配色识符，失败返回false
     */
    public function colorAllocate(int $red, int $green, int $blue): int
    {
        return imagecolorallocate($this->resource, $red, $green, $blue);
    }

    /**
     * 分配带透明度颜色
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @param int $alpha 透明度[0 ~ 127]
     * @return int 成功返回配色识符，失败返回false
     */
    public function colorAllocateAlpha(int $red, int $green, int $blue, int $alpha): int
    {
        return imagecolorclosestalpha($this->resource, $red, $green, $blue, $alpha);
    }

    /**
     * 取得某像素的颜色索引
     * @param int $x x轴坐标
     * @param int $y y轴坐标
     * @return int
     */
    public function colorAt(int $x, int $y): int
    {
        return imagecolorat($this->resource, $x, $y);
    }

    /**
     * 取得与指定的颜色最接近的颜色索引
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @return int
     */
    public function colorClosest(int $red, int $green, int $blue): int
    {
        return imagecolorclosest($this->resource, $red, $green, $blue);
    }

    /**
     * 取得与指定的颜色加透明度最接近的颜色索引
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @param int $alpha 透明度[0 ~ 127]
     * @return int
     */
    public function colorClosestAlpha(int $red, int $green, int $blue, int $alpha): int
    {
        return imagecolorclosestalpha($this->resource, $red, $green, $blue, $alpha);
    }

    /**
     * 取得与给定颜色最接近的色度的黑白色的索引
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @return int
     */
    public function colorClosestHwb(int $red, int $green, int $blue): int
    {
        return imagecolorclosesthwb($this->resource, $red, $green, $blue);
    }

    /**
     * 取消图像颜色分配
     * @param int $color 颜色索引
     * @return bool
     */
    public function colorDeallocate(int $color): bool
    {
        return imagecolordeallocate($this->resource, $color);
    }

    /**
     * 取得指定颜色的索引值
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @return int 如果颜色不在图像的调色板中，返回-1
     */
    public function colorExact(int $red, int $green, int $blue): int
    {
        return imagecolorexact($this->resource, $red, $green, $blue);
    }

    /**
     * 取得指定的颜色加透明度的索引值
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @param int $alpha 透明度[0 ~ 127]
     * @return int 如果颜色不在图像的调色板中，返回-1
     */
    public function colorExactAlpha(int $red, int $green, int $blue, int $alpha): int
    {
        return imagecolorexactalpha($this->resource, $red, $green, $blue, $alpha);
    }

    /**
     * 使一个图像中调色板版本的颜色与真彩色版本更能匹配
     * @param resource $image2 必须是调色板图像，而且和 image1 的大小必须相同
     * @return bool
     */
    public function colorMatch($image2): bool
    {
        return imagecolormatch($this->resource, $image2);
    }

    /**
     * 取得指定颜色的索引值或有可能得到的最接近的替代值
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @return int
     */
    public function colorResolve(int $red, int $green, int $blue): int
    {
        return imagecolorresolve($this->resource, $red, $green, $blue);
    }

    /**
     * 取得指定颜色 + alpha 的索引值或有可能得到的最接近的替代值
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @param int $alpha 透明度[0 ~ 127]
     * @return int
     */
    public function colorResolveAlpha(int $red, int $green, int $blue, int $alpha): int
    {
        return imagecolorresolvealpha($this->resource, $red, $green, $blue, $alpha);
    }

    /**
     * 给指定调色板索引设定颜色
     * @param int $index 索引
     * @param int $red   RGB成分[红]
     * @param int $green RGB成分[绿]
     * @param int $blue  RGB成分[蓝]
     * @param int $alpha 透明度[0 ~ 127]
     */
    public function colorSet(int $index, int $red, int $green, int $blue, int $alpha = 0)
    {
        imagecolorset($this->resource, $index, $red, $green, $blue, $alpha);
    }

    /**
     * 取得颜色索引的信息
     * @param int $index 索引
     * @return array 具有 red，green，blue 和 alpha 的键名的关联数组
     */
    public function colorsForIndex(int $index): array
    {
        return imagecolorsforindex($this->resource, $index);
    }

    /**
     * 取得一幅图像的调色板中颜色的数目
     * @return int
     */
    public function colorStotal(): int
    {
        return imagecolorstotal($this->resource);
    }

    /**
     * 将某个颜色定义为透明色
     * @param int|null $color 颜色索引
     * @return int 返回新透明色的标识符
     */
    public function colorTransparent(int $color = null): int
    {
        return imagecolortransparent($this->resource, $color);
    }

    /**
     * 用系数 div 和 offset 申请一个 3x3 的卷积矩阵
     * @param array $matrix 矩阵
     * @param float $div    卷积结果的除数
     * @param float $offset 颜色偏移
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function convolution(array $matrix, float $div, float $offset): bool
    {
        return imageconvolution($this->resource, $matrix, $div, $offset);
    }

    /**
     * 拷贝图像的一部分
     * @param mixed $src_im 要拷贝图像的资源对象或者图像文件路径
     * @param int   $dst_x  目标开始坐标x轴
     * @param int   $dst_y  目标开始坐标y轴
     * @param int   $src_x  拷贝开始坐标x轴
     * @param int   $src_y  拷贝开始坐标y轴
     * @param int   $src_w  拷贝宽度
     * @param int   $src_h  拷贝高度
     * @return bool 成功时返回 TRUE， 或者在失败时返回 FALSE。
     */
    public function copy($src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_w, int $src_h): bool
    {
        if (is_string($src_im)) {
            $src_im = $this->createFrom($src_im);
        }
        return imagecopy($this->resource, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
    }

    /**
     * 拷贝并合并图像的一部分
     * @param mixed $src_im 要拷贝图像的资源对象或者图像文件路径
     * @param int   $dst_x  目标开始坐标x轴
     * @param int   $dst_y  目标开始坐标y轴
     * @param int   $src_x  拷贝开始坐标x轴
     * @param int   $src_y  拷贝开始坐标y轴
     * @param int   $src_w  拷贝宽度
     * @param int   $src_h  拷贝高度
     * @param int   $pct    合并程度，0-100
     * @return bool
     */
    public function copyMerge($src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_w, int $src_h, int $pct): bool
    {
        if (is_string($src_im)) {
            $src_im = $this->createFrom($src_im);
        }
        return imagecopymerge($this->resource, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
    }

    /**
     * 用灰度拷贝并合并图像的一部分
     * @param mixed $src_im 要拷贝图像的资源对象或者图像文件路径
     * @param int   $dst_x  目标开始坐标x轴
     * @param int   $dst_y  目标开始坐标y轴
     * @param int   $src_x  拷贝开始坐标x轴
     * @param int   $src_y  拷贝开始坐标y轴
     * @param int   $src_w  拷贝宽度
     * @param int   $src_h  拷贝高度
     * @param int   $pct    合并程度，0-100
     * @return bool
     */
    public function copyMergeGray($src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_w, int $src_h, int $pct): bool
    {
        if (is_string($src_im)) {
            $src_im = $this->createFrom($src_im);
        }
        return imagecopymergegray($this->resource, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct);
    }

    /**
     * 重采样拷贝部分图像并调整大小
     * @param mixed $src_im 要拷贝图像的资源对象或者图像文件路径
     * @param int   $dst_x  目标开始坐标x轴
     * @param int   $dst_y  目标开始坐标y轴
     * @param int   $src_x  拷贝开始坐标x轴
     * @param int   $src_y  拷贝开始坐标y轴
     * @param int   $dst_w  目标宽度
     * @param int   $dst_h  目标高度
     * @param int   $src_w  源宽度
     * @param int   $src_h  源高度
     * @return bool
     */
    public function copyResampled($src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_w, int $dst_h, int $src_w, int $src_h): bool
    {
        if (is_string($src_im)) {
            $src_im = $this->createFrom($src_im);
        }
        return imagecopyresampled($this->resource, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }

    /**
     * 拷贝部分图像并调整大小
     * @param mixed $src_im 要拷贝图像的资源对象或者图像文件路径
     * @param int   $dst_x  目标开始坐标x轴
     * @param int   $dst_y  目标开始坐标y轴
     * @param int   $src_x  拷贝开始坐标x轴
     * @param int   $src_y  拷贝开始坐标y轴
     * @param int   $dst_w  目标宽度
     * @param int   $dst_h  目标高度
     * @param int   $src_w  源宽度
     * @param int   $src_h  源高度
     * @return bool
     */
    public function copyResized($src_im, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_w, int $dst_h, int $src_w, int $src_h): bool
    {
        if (is_string($src_im)) {
            $src_im = $this->createFrom($src_im);
        }
        return imagecopyresized($this->resource, $src_im, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
    }

    /**
     * 新建一个基于调色板的图像
     * @param int  $width     宽
     * @param int  $height    高
     * @param bool $truecolor 是否真彩色
     * @return resource 失败时返回false
     */
    public function create(int $width, int $height, bool $truecolor = true)
    {
        if ($truecolor) {
            $resource = imagecreatetruecolor($width, $height);
        } else {
            $resource = imagecreate($width, $height);
        }

        return $resource;
    }

    /**
     * 从指定资源创建
     * @param string      $filename 文件路径
     * @param string|null $from     指定资源类型，不指定则自动检测
     * @param array       $options  其他选项，目前仅对gd2part有效
     * @return resource 失败时返回false
     */
    public function createFrom(string $filename, string $from = null, array $options = []): bool
    {
        if (is_null($from)) {
            $org_filename = $this->file;
            $this->file = $filename;
            $from = $this->getType();
            $this->file = $org_filename;
        }
        switch (strtolower($from)) {
            case 'bmp':
                $resource = imagecreatefrombmp($filename);
                break;
            case 'gd2' :
                $resource = imagecreatefromgd2($filename);
                break;
            case 'gd2part':
                $srcX = $options['srcX'];
                $srcY = $options['srcY'];
                $width = $options['width'];
                $height = $options['height'];
                $resource = imagecreatefromgd2part($filename, $srcX, $srcY, $width, $height);
                break;
            case 'gd' :
                $resource = imagecreatefromgd($filename);
                break;
            case 'gif' :
                $resource = imagecreatefromgif($filename);
                break;
            case 'jpg':
            case 'jpeg' :
                $resource = imagecreatefromjpeg($filename);
                break;
            case 'png' :
                $resource = imagecreatefrompng($filename);
                break;
            case 'string' :
                $resource = imagecreatefromstring($filename);
                break;
            case 'wbmp' :
                $resource = imagecreatefromwbmp($filename);
                break;
            case 'webp' :
                $resource = imagecreatefromwebp($filename);
                break;
            case 'xbm' :
                $resource = imagecreatefromxbm($filename);
                break;
            case 'xpm' :
                $resource = imagecreatefromxpm($filename);
                break;
            default :
                $resource = false;
        }

        return $resource;
    }

    /**
     * 裁剪图像
     *
     * 参数 `$rect` :
     *   [x, y, width, height]
     * @param array $rect 裁剪区域
     * @return resource 失败时返回false
     */
    public function crop(array $rect)
    {
        $resource = imagecrop($this->resource, $rect);
        $this->resource = $resource;
        return $resource;
    }

    /**
     * 使用其中一种可用模式自动裁剪图像
     * @param int   $mode      IMG_CROP_*敞亮
     * @param float $threshold 容忍度，以百分比为单位
     * @param int   $color     颜色标识
     * @return resource
     */
    public function cropAuto(int $mode = -1, float $threshold = 0.5, int $color = -1)
    {
        $resource = imagecropauto($this->resource, $mode, $threshold, $color);
        $this->resource = $resource;
        return $resource;
    }

    /**
     * 画一虚线
     * @param int $x1    开始坐标x轴
     * @param int $y1    开始坐标y轴
     * @param int $x2    结束坐标x轴
     * @param int $y2    结束坐标y轴
     * @param int $color 颜色标识
     * @return bool
     * @deprecated 反对使用本函数。应该用 imagesetstyle() 和 imageline() 的组合替代之
     */
    public function dashedLine(int $x1, int $y1, int $x2, int $y2, int $color): bool
    {
        return imagedashedline($this->resource, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * 销毁一图像
     * @param resource $image 图像资源
     * @return bool
     */
    public function destroy($image): bool
    {
        return imagedestroy($image);
    }

    /**
     * 画一个椭圆
     * @param int $cx     中间的 X 坐标
     * @param int $cy     中间的 Y 坐标
     * @param int $width  宽度
     * @param int $height 高度
     * @param int $color  颜色标识
     * @return bool
     */
    public function ellipse(int $cx, int $cy, int $width, int $height, int $color): bool
    {
        return imageellipse($this->resource, $cx, $cy, $width, $height, $color);
    }

    /**
     * 区域填充，即与 x, y 点颜色相同且相邻的点都会被填充
     * @param int $x     X坐标
     * @param int $y     Y坐标
     * @param int $color 颜色标识
     * @return bool
     */
    public function fill(int $x, int $y, int $color): bool
    {
        return imagefill($this->resource, $x, $y, $color);
    }

    /**
     * 画一椭圆弧且填充
     * @param int $cx     中心点x轴
     * @param int $cy     中心点y轴
     * @param int $width  宽度
     * @param int $height 高度
     * @param int $start  起点角度，0°为3点钟方向
     * @param int $end    结束角度，0°为3点钟方向
     * @param int $color  颜色标识
     * @param int $style  类型，IMG_ARC_*常量
     * @return bool
     */
    public function filledArc(int $cx, int $cy, int $width, int $height, int $start, int $end, int $color, int $style): bool
    {
        return imagefilledarc($this->resource, $cx, $cy, $width, $height, $start, $end, $color, $style);
    }

    /**
     * 画一椭圆并填充
     * @param int $cx     中心点x轴
     * @param int $cy     中心点y轴
     * @param int $width  宽度
     * @param int $height 高度
     * @param int $color  颜色标识
     * @return bool
     */
    public function filledEllipse(int $cx, int $cy, int $width, int $height, int $color): bool
    {
        return imagefilledellipse($this->resource, $cx, $cy, $width, $height, $color);
    }

    /**
     * 画一多边形并填充
     *
     * 参数 `$points` :
     *   按顺序包含有多边形各顶点的 x 和 y 坐标的数组
     * @param array $points     顶点数组
     * @param int   $num_points 顶点的总数，必须大于3
     * @param int   $color      颜色标识
     * @return bool
     */
    public function filledPolygon(array $points, int $num_points, int $color): bool
    {
        return imagefilledpolygon($this->resource, $points, $num_points, $color);
    }

    /**
     * 画一矩形并填充
     * @param int $x1    左上角x轴
     * @param int $y1    左上角y轴
     * @param int $x2    右下角x轴
     * @param int $y2    右下角y轴
     * @param int $color 颜色标识
     * @return bool
     */
    public function filledRectangle(int $x1, int $y1, int $x2, int $y2, int $color): bool
    {
        return imagefilledrectangle($this->resource, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * 区域填充到指定颜色的边界为止
     * @param int $x      开始点x轴
     * @param int $y      开始点y轴
     * @param int $border 边界颜色标识
     * @param int $color  填充颜色标识
     * @return bool
     */
    public function fillToBorder(int $x, int $y, int $border, int $color): bool
    {
        return imagefilltoborder($this->resource, $x, $y, $border, $color);
    }

    /**
     * 使用过滤器
     * @param int      $filtertype 常量IMG_FILTER_*
     * @param int|null $arg1       可选参数1
     * @param int|null $arg2       可选参数2
     * @param int|null $arg3       可选参数3
     * @param int|null $arg4       可选参数4
     * @return bool
     */
    public function filter(int $filtertype, int $arg1 = null, int $arg2 = null, int $arg3 = null, int $arg4 = null): bool
    {
        return imagefilter($this->resource, $filtertype, $arg1, $arg2, $arg3, $arg4);
    }

    /**
     * 使用给定模式翻转图像
     * @param int $mode 常量IMG_FLIP_*
     * @return bool
     */
    public function flip(int $mode): bool
    {
        return imageflip($this->resource, $mode);
    }

    /**
     * 取得字体高度
     * @param int $font 字体标识
     * @return int
     */
    public static function fontHeight(int $font): int
    {
        return imagefontheight($font);
    }

    /**
     * 取得字体宽度
     * @param int $font 字体标识
     * @return int
     */
    public static function fontWidth(int $font): int
    {
        return imagefontwidth($font);
    }

    /**
     * 给出一个使用 FreeType 2 字体的文本框
     * @param float  $size      字体的尺寸
     * @param float  $angle     使文本具有保密性的角度。
     * @param string $fontfile  字体文件路径
     * @param string $text      要渲染的字符串
     * @param array  $extrainfo 其他设置
     * @return array 数组含8个元素，失败时返回false
     */
    public static function ftbbox(float $size, float $angle, string $fontfile, string $text, array $extrainfo = null): array
    {
        return imageftbbox($size, $angle, $fontfile, $text, $extrainfo);
    }

    /**
     * 使用 FreeType 2 字体将文本写入图像
     * @param float  $size      字体的尺寸
     * @param float  $angle     使文本具有保密性的角度。
     * @param int    $x         左上角x轴
     * @param int    $y         左上角y轴
     * @param int    $color     颜色标识
     * @param string $fontfile  字体文件路径
     * @param string $text      要渲染的字符串
     * @param array  $extrainfo 其他设置
     * @return array 数组含8个元素，失败时返回false
     */
    public function fttext(float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text, array $extrainfo = null): array
    {
        return imagefttext($this->resource, $size, $angle, $x, $y, $color, $fontfile, $text, $extrainfo);
    }

    /**
     * 对 GD 图像应用 gamma 修正
     * @param float $inputgamma  输入gamma
     * @param float $outputgamma 输出gamma
     * @return bool
     */
    public function gammaCorrect(float $inputgamma, float $outputgamma): bool
    {
        return imagegammacorrect($this->resource, $inputgamma, $outputgamma);
    }

    /**
     * 取得剪切矩形
     * @return array 4个元素
     */
    public function getClip(): array
    {
        return imagegetclip($this->resource);
    }

    /**
     * 捕捉整个屏幕
     * @return resource
     * @deprecated 不建议使用
     * @notice     该方法仅在windows下有效
     */
    public static function grabScreen()
    {
        return imagegrabscreen();
    }

    /**
     * 捕捉指定窗口
     * @param int $window_handle 句柄ID
     * @param int $client_area   包括应用程序窗口的客户端区域。
     * @return resource
     * @deprecated 不建议使用
     * @notice     该方法仅在windows下有效
     */
    public static function grabWindow(int $window_handle, int $client_area = 0)
    {
        return imagegrabwindow($window_handle, $client_area);
    }

    /**
     * 激活或禁止隔行扫描
     * @param int|null $interlace 1激活，0禁止
     * @return int 返回当前状态
     */
    public function interlace(int $interlace = null): int
    {
        return imageinterlace($this->resource, $interlace);
    }

    /**
     * 检查图像是否为真彩色图像
     * @return bool
     */
    public function isTrueColor(): bool
    {
        return imageistruecolor($this->resource);
    }

    /**
     * 设定 alpha 混色标志以使用绑定的 libgd 分层效果
     * @param int $effect 常量IMG_EFFECT_*
     * @return bool
     */
    public function layerEffect(int $effect): bool
    {
        return imagelayereffect($this->resource, $effect);
    }

    /**
     * 画一条线段
     * @param int $x1    起点x轴
     * @param int $y1    起点y轴
     * @param int $x2    终点x轴
     * @param int $y2    终点y轴
     * @param int $color 颜色标识
     * @return bool
     */
    public function line(int $x1, int $y1, int $x2, int $y2, int $color): bool
    {
        return imageline($this->resource, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * 载入新字体
     * @param string $file 字体文件路径
     * @return int 返回字体标识
     */
    public static function loadFont(string $file): int
    {
        return imageloadfont($file);
    }

    /**
     * 绘制一个开放多边形
     * @param array $points     顶点数组
     * @param int   $num_points 顶点数量
     * @param int   $color      颜色标识
     * @return bool
     * @since PHP7.2
     */
    public function openPolygon(array $points, int $num_points, int $color): bool
    {
        return imageopenpolygon($this->resource, $points, $num_points, $color);
    }

    /**
     * 将调色板从一幅图像拷贝到另一幅
     * @param resource $destination 接收方图像资源
     */
    public function paletteCopy($destination)
    {
        imagepalettecopy($destination, $this->resource);
    }

    /**
     * 将基于调色板的图像转换为真颜色
     * @return bool
     */
    public function paletteToTrueColor(): bool
    {
        return imagepalettetotruecolor($this->resource);
    }

    /**
     * 画一个多边形
     * @param array $points     顶点数组
     * @param int   $num_points 顶点数量
     * @param int   $color      颜色标识
     * @return bool
     */
    public function polygon(array $points, int $num_points, int $color): bool
    {
        return imagepolygon($this->resource, $points, $num_points, $color);
    }

    /**
     * 给出一个使用 PostScript Type1 字体的文本方框
     * @param string   $text 要写入的文本
     * @param resource $font 字体标识
     * @param int      $size 字体大小
     * @return array
     * @deprecated PHP7已移除该方法
     */
    public static function psbbox(string $text, $font, int $size): array
    {
        return imagepsbbox($text, $font, $size);
    }

    /**
     * 改变字体中的字符编码矢量
     * @param resource $font_index   字体标识
     * @param string   $encodingfile IsoLatin1.enc and IsoLatin2.enc.
     * @return bool
     * @deprecated PHP7已移除该方法
     */
    public static function psEncodeFont($font_index, string $encodingfile): bool
    {
        return imagepsencodefont($font_index, $encodingfile);
    }

    /**
     * 扩充或精简字体
     * @param resource $font_index 字体标识
     * @param float    $extend     扩展的值，必须大于 0。小于1则是精简该字体
     * @return bool
     * @deprecated PHP7已移除该方法
     */
    public static function psExtendFont($font_index, float $extend): bool
    {
        return imagepsextendfont($font_index, $extend);
    }

    /**
     * 释放一个 PostScript Type 1 字体所占用的内存
     * @param resource $font_index 字体标识
     * @return bool
     * @deprecated PHP7已移除该方法
     */
    public static function psFreeFont($font_index): bool
    {
        return imagepsfreefont($font_index);
    }

    /**
     * 从文件中加载一个 PostScript Type 1 字体
     * @param string $filename 字体文件路径
     * @return resource 失败时返回false
     * @deprecated PHP7已移除该方法
     */
    public static function psLoadFont(string $filename)
    {
        return imagepsloadfont($filename);
    }

    /**
     * 倾斜某字体
     * @param resource $font_index 字体标识
     * @param float    $slant      倾斜度
     * @return bool
     * @deprecated PHP7已移除该方法
     */
    public static function psSlantFont($font_index, float $slant): bool
    {
        return imagepsslantfont($font_index, $slant);
    }

    /**
     * 用 PostScript Type1 字体把文本字符串画在图像上
     * @param string   $text            要写入的文本
     * @param resource $font_index      字体标识
     * @param int      $size            字体大小
     * @param int      $foreground      写入的字体的颜色标识
     * @param int      $background      背景颜色标识
     * @param int      $x               左下角起点x轴
     * @param int      $y               左下角起点y轴
     * @param int      $space           字体间距
     * @param int      $tightness       字符间距
     * @param float    $angle           角度
     * @param int      $antialias_steps 可以控制防混色文本使用的颜色的数目
     * @return array 有4个元素，失败返回false
     * @deprecated PHP7已移除该方法
     */
    public function psText(string $text, $font_index, int $size, int $foreground, int $background, int $x, int $y, int $space = 0, int $tightness = 0, float $angle = 0.0, int $antialias_steps = 4): array
    {
        return imagepstext($this->resource, $text, $font_index, $size, $foreground, $background, $x, $y, $space, $tightness, $angle, $antialias_steps);
    }

    /**
     * 画一个矩形
     * @param int $x1    左上角坐标x轴
     * @param int $y1    左上角坐标y轴
     * @param int $x2    右下角坐标x轴
     * @param int $y2    右下角坐标y轴
     * @param int $color 颜色标识
     * @return bool
     */
    public function rectangle(int $x1, int $y1, int $x2, int $y2, int $color): bool
    {
        return imagerectangle($this->resource, $x1, $y1, $x2, $y2, $color);
    }

    /**
     * 获取或设置图像的分辨率
     * @param int|null $res_x 横向分辨率
     * @param int|null $res_y 纵向分辨率
     * @return array|bool 获取时返回数组[x,y]，设置时返回结果
     */
    public function resolution(int $res_x = null, int $res_y = null)
    {
        if (is_null($res_x) && is_null($res_y)) {
            return imageresolution($this->resource);
        }
        return imageresolution($this->resource, $res_x, $res_y);
    }

    /**
     * 用给定角度旋转图像
     * @param float $angle              角度
     * @param int   $bgd_color          指定旋转后未覆盖区域的颜色
     * @param int   $ignore_transparent 如果被设为非零值，则透明色会被忽略（否则会被保留）。
     * @return resource
     */
    public function rotate(float $angle, int $bgd_color, int $ignore_transparent = 0)
    {
        $resource = imagerotate($this->resource, $angle, $bgd_color, $ignore_transparent);
        $this->resource = $resource;
        return $resource;
    }

    /**
     * 设置标记以在保存 PNG 图像时保存完整的 alpha 通道信息（与单一透明色相反）
     * @param bool $saveflag 是否保存透明（alpha）通道。默认 FALSE。
     * @return bool
     */
    public function saveAlpha(bool $saveflag): bool
    {
        return imagesavealpha($this->resource, $saveflag);
    }

    /**
     * 使用给定的新宽度和高度缩放图像
     * @param int $new_width  新宽度
     * @param int $new_height 新高度，-1表示自动计算
     * @param int $mode       模式
     * @return resource
     */
    public function scale(int $new_width, int $new_height = -1, int $mode = 3)
    {
        $resource = imagescale($this->resource, $new_width, $new_height, $mode);
        $this->resource = $resource;
        return $resource;
    }

    /**
     * 设定画线用的画笔图像
     * @param resource $brush 画笔图像
     * @return bool
     */
    public function setBrush($brush): bool
    {
        return imagesetbrush($this->resource, $brush);
    }

    /**
     * 设置剪切矩形
     * @param int $x1 左上角坐标x轴
     * @param int $y1 左上角坐标y轴
     * @param int $x2 右下角坐标x轴
     * @param int $y2 右下角坐标y轴
     * @return bool
     * @since PHP7.2
     */
    public function setClip(int $x1, int $y1, int $x2, int $y2): bool
    {
        return imagesetclip($this->resource, $x1, $y1, $x2, $y2);
    }

    /**
     * 设置插值方法
     * @param int $method 方法常量
     * @return bool
     */
    public function setInterpolation(int $method = 3): bool
    {
        return imagesetinterpolation($this->resource, $method);
    }

    /**
     * 画一个单一像素
     * @param int $x     坐标x轴
     * @param int $y     坐标y轴
     * @param int $color 颜色标识
     * @return bool
     */
    public function setPixel(int $x, int $y, int $color): bool
    {
        return imagesetpixel($this->resource, $x, $y, $color);
    }

    /**
     * 设定画线的风格
     * @param array $style 像素组成的数组
     * @return bool
     */
    public function setStyle(array $style): bool
    {
        return imagesetstyle($this->resource, $style);
    }

    /**
     * 设定画线的宽度
     * @param int $thickness 宽度像素
     * @return bool
     */
    public function setThickness(int $thickness): bool
    {
        return imagesetthickness($this->resource, $thickness);
    }

    /**
     * 设定用于填充的贴图
     * @param resource $tile 贴图
     * @return bool
     */
    public function setTile($tile): bool
    {
        return imagesettile($this->resource, $tile);
    }

    /**
     * 水平地画一行字符串
     * @param int    $font   字体标识
     * @param int    $x      左上角坐标x轴
     * @param int    $y      左上角坐标y轴
     * @param string $string 字符串
     * @param int    $color  颜色标识
     * @return bool
     */
    public function stringHorizontal(int $font, int $x, int $y, string $string, int $color): bool
    {
        return imagestring($this->resource, $font, $x, $y, $string, $color);
    }

    /**
     * 垂直地画一行字符串
     * @param int    $font   字体标识
     * @param int    $x      左上角坐标x轴
     * @param int    $y      左上角坐标y轴
     * @param string $string 字符串
     * @param int    $color  颜色标识
     * @return bool
     */
    public function stringUp(int $font, int $x, int $y, string $string, int $color): bool
    {
        return imagestringup($this->resource, $font, $x, $y, $string, $color);
    }

    /**
     * 取得图像宽度
     * @return int
     */
    public function sx(): int
    {
        return imagesx($this->resource);
    }

    /**
     * 取得图像高度
     * @return int
     */
    public function sy(): int
    {
        return imagesy($this->resource);
    }

    /**
     * 将真彩色图像转换为调色板图像
     * @param bool $dither  指明图像是否被抖动
     * @param int  $ncolors 设定调色板中被保留的颜色的最大数目
     * @return bool
     */
    public function trueColorToPalette(bool $dither, int $ncolors): bool
    {
        return imagetruecolortopalette($this->resource, $dither, $ncolors);
    }

    /**
     * 取得使用 TrueType 字体的文本的范围
     * @param float  $size     像素单位的字体大小
     * @param float  $angle    将被度量的角度大小
     * @param string $fontfile 字体文件路径
     * @param string $text     要度量的字符串
     * @return array 8个元素，失败时返回false
     */
    public static function ttfbbox(float $size, float $angle, string $fontfile, string $text): array
    {
        return imagettfbbox($size, $angle, $fontfile, $text);
    }

    /**
     * 用 TrueType 字体向图像写入文本
     * @param float  $size     像素单位的字体大小
     * @param float  $angle    将被度量的角度大小
     * @param int    $x        左上角坐标x轴
     * @param int    $y        左上角坐标y轴
     * @param int    $color    颜色标识
     * @param string $fontfile 字体文件路径
     * @param string $text     要写入的文本
     * @return array 失败时返回false
     */
    public function ttftext(float $size, float $angle, int $x, int $y, int $color, string $fontfile, string $text): array
    {
        return imagettftext($this->resource, $size, $angle, $x, $y, $color, $fontfile, $text);
    }

    /**
     * 返回当前 PHP 版本所支持的图像类型
     * @return int 以比特字段方式返回
     */
    public static function types(): int
    {
        return imagetypes();
    }

    /**
     * 将二进制 IPTC 数据嵌入到一幅 JPEG 图像中
     * @param string $iptcdata IPTC数据
     * @param int    $spool    标识
     * @return bool|string
     */
    public function iptcEmbed(string $iptcdata, int $spool = 0)
    {
        return iptcembed($iptcdata, $this->file, $spool);
    }

    /**
     * 将二进制 IPTC 块解析为单个标记
     * @param string $iptcblock IPTC块
     * @return array 失败时返回false
     */
    public static function iptcParse(string $iptcblock): array
    {
        return iptcparse($iptcblock);
    }

    /**
     * 将 JPEG 图像文件转换为 WBMP 图像文件
     * @param $jpegname
     * @param $wbmpname
     * @param $dest_height
     * @param $dest_width
     * @param $threshold
     * @return bool
     * @deprecated 7.2.0 Use imagecreatefromjpeg() and imagewbmp() instead
     */
    public static function jpeg2Wbmp($jpegname, $wbmpname, $dest_height, $dest_width, $threshold): bool
    {
        return jpeg2wbmp($jpegname, $wbmpname, $dest_height, $dest_width, $threshold);
    }

    /**
     * 将 PNG 图像文件转换为 WBMP 图像文件
     * @param $pngname
     * @param $wbmpname
     * @param $dest_height
     * @param $dest_width
     * @param $threshold
     * @return bool
     * @deprecated 7.2.0 Use imagecreatefrompng() and imagewbmp() instead
     */
    public static function png2Wbmp($pngname, $wbmpname, $dest_height, $dest_width, $threshold): bool
    {
        return png2wbmp($pngname, $wbmpname, $dest_height, $dest_width, $threshold);
    }
}
