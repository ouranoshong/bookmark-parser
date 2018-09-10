<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 2018/9/4
 * Time: 11:19 AM
 */

namespace Bookmark\Parser\Node;


class AbstractNode
{
    const TYPE_DIR = 'dir';
    const TYPE_BOOKMARK = 'bookmark';

    public $id;

    public $name;

    public $type = self::TYPE_BOOKMARK;

    public $addDate;

    public $lastModified;

    public $children = [];

    public $description;
}