<?php
/**
 * Created by PhpStorm.
 * User: hong
 * Date: 2018/9/4
 * Time: 11:10 AM
 */

namespace Bookmark\Parser;


use Bookmark\Parser\Node\AbstractNode;
use Bookmark\Parser\Node\BookmarkNode;
use Bookmark\Parser\Node\DirectoryNode;

class Parser
{

    /**
     * @var AbstractNode
     */
    private $context;

    /**
     * @var AbstractNode
     */
    private $cursor;

    /**
     * @var array
     */
    private $contextStack = [];

    public function parseLineByLine($line)
    {
        $line = $this->beautifyLine($line);

        if (preg_match("/^\<TITLE\>(.+)\<\/TITLE\>$/u", $line, $m)) {
            $node = new BookmarkNode();
            $node->name = $m[1];
            $node->children = [];
            $this->context = $this->cursor = $node;
        }

        if (preg_match("/<DL>/", $line)) {

            array_push($this->contextStack, $this->context);
            $this->context = $this->cursor;

        }

        if (preg_match("/<\/DL>/", $line)) {

            $this->cursor = $this->context;
            $this->context = array_pop($this->contextStack);

        }

        if (preg_match("/<DT>(<H3.+<\/H3>)<\/DT>/u", $line, $m)) {

            $doc = new \DOMDocument();
            $doc->loadHTML($m[1]);
            $domNode = $doc->getElementsByTagName("h3")->item(0);

            if (null === $domNode) return;
            $node = new DirectoryNode();
            $node->id = md5($m[1]);
            $node->name = $domNode->textContent;
            $node->addDate = (int)$domNode->getAttribute("add_date");
            $node->lastModified = (int)$domNode->getAttribute("last_modified");
            $node->children = [];
            $this->cursor = $node;
            $this->context->children[] = $this->cursor;

        }

        if (preg_match("/<DT>(<A.+<\/A>)<\/DT>/u", $line, $m)) {

            $doc = new \DOMDocument('1.0', 'UTF-8');

            @$doc->loadHTML($m[1]);

            $domNode = $doc->getElementsByTagName("a")->item(0);

            if (null === $domNode) return;

            $node = new BookmarkNode();
            $node->id = md5($m[1]);
            $node->children = [];
            $node->name = $domNode->textContent;
            $node->addDate = (int)$domNode->getAttribute("add_date");
            $node->lastModified = (int)$domNode->getAttribute("last_modified");
            $node->url = $domNode->getAttribute('href');
            $node->icon = $domNode->getAttribute('icon');
            $node->iconUrl = $domNode->getAttribute('icon_uri');
            $this->cursor = $node;
            $this->context->children[] = $this->cursor;

        }

        if (preg_match("/<DD>(.+)/u", $line, $m)) {
            $this->cursor->description = $m[1];
        }
    }

    private function beautifyLine($line)
    {
        $line = trim($line);
        $line = preg_replace("/<p>/", '', $line);
        $line = preg_replace("/(<!DOCTYPE.+)/", '', $line);
        $line = preg_replace("/(<META.+)/u", '', $line);
        $line = preg_replace("/<HR>/", '', $line);
        return preg_replace("/<DT>.+/u", "$0</DT>", $line);
    }

    public function getBookmarks()
    {
        return $this->context;
    }

    public static function fromFile($filename)
    {
        $file = fopen($filename, 'r');

        $parser = self::fromResource($file);

        fclose($file);

        return $parser;
    }

    public static function fromResource($fp)
    {
        $parser = new Parser();

        while (!feof($fp))
        {
            // Get the current line that the file is reading
            $currentLine = fgets($fp) ;

            $parser->parseLineByLine($currentLine);
        }

        return $parser;
    }

    public static function fromText($text)
    {
        $parser = new Parser();

        foreach(explode("\n", $text) as $line) {
            $parser->parseLineByLine($line);
        }

        return $parser;
    }
}