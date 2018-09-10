<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 2018/9/4
 * Time: 11:13 AM
 */

namespace Bookmark\Parser\Node;


class DirectoryNode extends AbstractNode
{
    public $type = self::TYPE_DIR;
}