<?php

namespace fize\image;

/**
 * 图像元数据操作类
 * @notice 需要开启扩展ext-exif
 */
class Exif
{

    /**
     * 当前使用的图像文件完整路径
     * @var string
     */
    private $filename;


    /**
     * 构造
     * @param string $filename 完整含目录文件名
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    /**
     * 获取当前图像的类型
     * @return int 返回值和 getimagesize() 返回的数组中的索引 2 的值是一样的，但本函数快得多
     */
    public function imagetype(): int
    {
        return exif_imagetype($this->filename);
    }

    /**
     * 从 JPEG 或 TIFF 文件中读取 EXIF 头信息
     * @param string|null $sections  FILE|COMPUTED|ANY_TAG|IFD0|THUMBNAIL|COMMENT|EXIF
     * @param bool        $arrays    指定了是否每个区段都成为一个数组
     * @param bool        $thumbnail 是否读取缩略图本身。否则只读取标记数据。
     * @return array 如果没有可供返回的数据将返回 FALSE
     */
    public function readData(string $sections = null, bool $arrays = false, bool $thumbnail = false): array
    {
        return exif_read_data($this->filename, $sections, $arrays, $thumbnail);
    }

    /**
     * 获取指定索引的头名称
     * @param int $index 要查找的标签名称的 ID。
     * @return string 返回头名称。 如果 index 参数不是预定义的 EXIF 标签 id，则返回 FALSE
     */
    public static function tagname(int $index): string
    {
        return exif_tagname($index);
    }

    /**
     * 取得嵌入在 TIFF 或 JPEG 图像中的缩略图
     * @param int|null $width     本字段将返回缩略图的宽
     * @param int|null $height    本字段将返回缩略图的高
     * @param int|null $imagetype 本字段将返回缩略图的图像的类型
     * @return string 缩略图字节流，可用于直接输出
     */
    public function thumbnail(int &$width = null, int &$height = null, int &$imagetype = null): string
    {
        return exif_thumbnail($this->filename, $width, $height, $imagetype);
    }
}
