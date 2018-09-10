<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 2018/9/4
 * Time: 11:14 AM
 */

namespace Bookmark\Parser\Node;

//addDate: tmp['A']['ADD_DATE'],
//        lastModified: tmp['A']['LAST_MODIFIED'],
//        name: tmp['A']['$t'],
//        type: 'bookmark',
//        url: tmp['A']['HREF'],
//        id: `${idQueue.join('-')}-${++subId}`,
//        iconUri: tmp['A']['ICON_URI'],
//        icon: tmp['A']['ICON']
/**
 *
 * Class BookmarkNode
 * @package Bookmark\Parser\Node
 */
class BookmarkNode extends AbstractNode
{
    public $type = self::TYPE_BOOKMARK;

    public $url;

    public $iconUrl;

    public $icon;
}