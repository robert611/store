<?php

namespace App\Service;

class Paginator
{
    public $thOnPage;
    public $pageId;
    public $items;

    public function __construct(int $thOnPage, $items, $pageId)
    {
        $this->thOnPage = $thOnPage;
        $this->items = $items;
        $this->pageId = $pageId;
    }

    public function paginator()
    {
        $itemsToReturn = [];

        for ($i = 1; $i <= $this->thOnPage; $i++)
        {
            $c = $i + ($this->pageId - 1) * $this->thOnPage; // 1 + (2 - 1) * 20 =

            if ($c <= count($this->items))
            {
                $b = $this->items[$c - 1];
                $itemsToReturn[] = $b;
            }
        }

        return $itemsToReturn;
    }

    public function getPages()
    {
        $episodeCount = count($this->items);
        $pages = $episodeCount / $this->thOnPage;
        $pages = (int) ceil($pages);

        return $pages;
    }

    public function getNextPage()
    {
        if($this->pageId != count($this->getPages()) && $this->pageId < count($this->getPages()) && $this->pageId != count($this->getPages()))
        {
            return $next = $this->pageId + 1;
        }
        else
        {
            return $next = NULL;
        }
    }

    public function getPreviousPage()
    {
        if($this->pageId != 1 )
        {
            return $previous = $this->pageId - 1;
        }
        else
        {
            return $previous = NULL;
        }
    }
}