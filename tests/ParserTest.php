<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 2018/9/6
 * Time: 10:23 AM
 */

namespace Test\Bookmark\Parser;


use Bookmark\Parser\Node\AbstractNode;
use Bookmark\Parser\Node\BookmarkNode;
use Bookmark\Parser\Node\DirectoryNode;
use Bookmark\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function testParseTitleTag()
    {
        $parser = new Parser();
        $parser->parseLineByLine(
            '<TITLE>bookmark</TITLE>'
        );
        $node = $parser->getBookmarks();
        $this->assertEquals('bookmark', $node->name);
    }

    public function testParseDLTag()
    {
        $parser = new Parser();

        $parser->parseLineByLine('<TITLE>bookmark</TITLE>');
        $parser->parseLineByLine('<DL>');
        $parser->parseLineByLine('  <DT><A>php</A>');
        $parser->parseLineByLine('</DL');

        $node = $parser->getBookmarks();

        $this->assertInstanceOf(BookmarkNode::class, $node);
        $this->assertEquals('bookmark', $node->name);

        /** @var DirectoryNode $child */
        foreach($node->children as $child) {
            $this->assertInstanceOf(BookmarkNode::class, $child);
            $this->assertEquals(AbstractNode::TYPE_BOOKMARK, $child->type);
            $this->assertEquals([], $child->children);
            $this->assertEquals('php', $child->name);
        }
    }

    public function testParseDTH3Tag()
    {
        $parser = new Parser();

        $parser->parseLineByLine('<TITLE>bookmark</TITLE>');
        $parser->parseLineByLine('<DL>');
        $parser->parseLineByLine('  <DT><H3 ADD_DATE="1536202333" LAST_MODIFIED="1536202333">php</H3>');
        $parser->parseLineByLine('</DL>');

        $node = $parser->getBookmarks();

        $this->assertInstanceOf(BookmarkNode::class, $node);
        $this->assertEquals('bookmark', $node->name);

        /** @var DirectoryNode $child */
        foreach($node->children as $child) {

            $this->assertInstanceOf(DirectoryNode::class, $child);
            $this->assertEquals(AbstractNode::TYPE_DIR, $child->type);
            $this->assertEquals(1536202333, $child->addDate);
            $this->assertEquals(1536202333, $child->lastModified);
            $this->assertEquals([], $child->children);
            $this->assertEquals('php', $child->name);

        }

    }

    public function testParseDTATag()
    {
        $parser = new Parser();

        $parser->parseLineByLine('<TITLE>bookmark</TITLE>');
        $parser->parseLineByLine('<DL>');
        $parser->parseLineByLine('  <DT><A ICON="icon" ICON_URI="icon_uri" HREF="link" ADD_DATE="1536202333" LAST_MODIFIED="1536202333">php</A>');
        $parser->parseLineByLine('</DL>');

        $node = $parser->getBookmarks();

        $this->assertInstanceOf(BookmarkNode::class, $node);
        $this->assertEquals('bookmark', $node->name);

        /** @var BookmarkNode $child */
        foreach($node->children as $child) {

            $this->assertInstanceOf(BookmarkNode::class, $child);
            $this->assertEquals(AbstractNode::TYPE_BOOKMARK, $child->type);
            $this->assertEquals(1536202333, $child->addDate);
            $this->assertEquals(1536202333, $child->lastModified);
            $this->assertEquals([], $child->children);
            $this->assertEquals('php', $child->name);

            $this->assertEquals('icon', $child->icon);
            $this->assertEquals('icon_uri', $child->iconUrl);
            $this->assertEquals('link', $child->url);
        }

    }

    public function testParseDDTag()
    {
        $parser = new Parser();

        $parser->parseLineByLine('<TITLE>bookmark</TITLE>');
        $parser->parseLineByLine('<DL>');
        $parser->parseLineByLine('  <DT><A>php</A>');
        $parser->parseLineByLine('  <DD> test description');
        $parser->parseLineByLine('</DL>');

        $node = $parser->getBookmarks();

        $this->assertInstanceOf(BookmarkNode::class, $node);
        $this->assertEquals('bookmark', $node->name);

        /** @var BookmarkNode $child */
        foreach($node->children as $child) {

            $this->assertInstanceOf(BookmarkNode::class, $child);
            $this->assertEquals(AbstractNode::TYPE_BOOKMARK, $child->type);
            $this->assertEquals('php', $child->name);
            $this->assertEquals(' test description', $child->description);
        }
    }

    public function testParseRecursive()
    {
        $parser = new Parser();

        $parser->parseLineByLine('<TITLE>bookmark</TITLE>');
        $parser->parseLineByLine('<DL>');
        $parser->parseLineByLine('  <DT><H3>php</H3>');
        $parser->parseLineByLine('  <DD> test description');
        $parser->parseLineByLine('  <DL>');
        $parser->parseLineByLine('      <DT><A>php</A>');
        $parser->parseLineByLine('      <DD> test description');
        $parser->parseLineByLine('  </DL>');
        $parser->parseLineByLine('</DL>');

        $node = $parser->getBookmarks();

        $this->assertInstanceOf(BookmarkNode::class, $node);
        $this->assertEquals('bookmark', $node->name);

        /** @var DirectoryNode $child */
        foreach($node->children as $child) {

            $this->assertInstanceOf(DirectoryNode::class, $child);
            $this->assertEquals(AbstractNode::TYPE_DIR, $child->type);
            $this->assertEquals('php', $child->name);
            $this->assertEquals(' test description', $child->description);

            /** @var BookmarkNode $childOfChild */
            foreach($child->children as $childOfChild) {

                $this->assertInstanceOf(BookmarkNode::class, $childOfChild);
                $this->assertEquals(AbstractNode::TYPE_BOOKMARK, $childOfChild->type);
                $this->assertEquals('php', $childOfChild->name);
                $this->assertEquals(' test description', $childOfChild->description);

            }
        }
    }
}