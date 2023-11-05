<?php

namespace unit\Services\Parser;

use Codeception\Test\Unit;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Services\Parser\ParserService;

class ParserTest extends Unit
{
    /**
     * @return void
     */
    public function testParsePage()
    {
        $service = $this->getParseService();

        $shopsRes = [];
        $page = 1;

        foreach ($service->parsePage($page) as $shops) {
            $shopsRes[] = $shops;

            if ($service->isLastPage) {
                break;
            }
        }

        /** @var Shop[] $shopsRes */
        $shopsRes = array_merge(...$shopsRes);

        static::assertCount(5, $shopsRes);
        static::assertCount(12,$shopsRes[1]->items);
    }

    /**
     * @return ParserService
     */
    private function getParseService(): ParserService
    {
        $mock = $this->getMockBuilder(ParserService::class)
            ->enableOriginalConstructor()
            ->onlyMethods(['getShopPageHtml', 'getMainPageHtml'])
            ->getMock();

        $mock->method('getShopPageHtml')->willReturnCallback(static function () {
            return file_get_contents(__DIR__ . '/testHtml/shopPageHtml.html');
        });

        $mock->method('getMainPageHtml')->willReturnCallback(static function () {
            return file_get_contents(__DIR__ . '/testHtml/mainPageHtml.html');
        });

        return $mock;
    }
}