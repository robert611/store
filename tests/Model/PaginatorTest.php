<?php

namespace App\Tests\Model;

use App\Model\Paginator;
use PHPUnit\Framework\TestCase;

class PaginatorTest extends TestCase
{

    public function testGetUnitsForThisPage()
    {
        $expectedFirstPage = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
        $expectedSecondPage = [21, 22, 23, 24];

        $units = array_merge($expectedFirstPage, $expectedSecondPage);

        $paginator = new Paginator(20, $units, 2);

        $array = $paginator->getUnitsForThisPage();

        $this->assertEquals($array, $expectedSecondPage);
    }

    public function testGetNumberOfPages()
    {
        $units = array_fill(0, 41, 1);

        $paginator = new Paginator(20, $units, 2);

        $pages = $paginator->getNumberOfPages();

        $this->assertEquals(3, $pages);
    }
}