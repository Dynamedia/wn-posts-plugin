<?php namespace Dynamedia\Posts\Classes\Body\Formblocks\Blocks;


class SectionBlock
{
    const view = 'dynamedia.posts::blocks.section';

    private $block;
    private $html;
    private $contents = [];


    public function __construct($block)
    {
        $this->block = $block;
        $this->parseBlock();
    }

    private function parseBlock()
    {
        // todo via the view
        $this->html = $this->block['block']['content'];
    }

    public function getHtml()
    {
        return $this->html;
    }

    public function getContents()
    {
        return $this->contents;
    }
}
