<?php

namespace unit;

use Codeception\Stub;
use Codeception\Test\Unit;
use Exception;
use Gotyefrid\ComebackpwParser\ComebackPwParser;
use Gotyefrid\ComebackpwParser\Models\ArmorItem;
use Gotyefrid\ComebackpwParser\Models\Coordinates;
use Gotyefrid\ComebackpwParser\Models\Shop;
use Gotyefrid\ComebackpwParser\Models\ShopItem;
use Gotyefrid\ComebackpwParser\Models\ShopOwner;

class ComebackPwParserTest extends Unit
{
    /**
     * @throws Exception
     */
    public function testParse()
    {
        //$parser = $this->getParser();
        $parser = new ComebackPwParser();
        static::assertInstanceOf(ShopItem::class, $parser->parse()[0]);
    }

    /**
     * @throws Exception
     */
    private function getParser(): ComebackPwParser
    {
        return Stub::make(ComebackPwParser::class, [
            'parse' => static function () {
                $item = new ArmorItem([
                    'name' => '123',
                    'price' => 1,
                    'count' => 1,
                    'cells' => 0,
                    'ep' => 12,
                    'stats' => [],
                ]);

                $shop = new Shop([
                    'id' => 1,
                    'owner' => new ShopOwner([
                        'id' => 1,
                        'nickname' => 1,
                    ]),
                    'name' => 1,
                    'coordinates' => new Coordinates(),
                    'items' => [],
                ]);


                $shopItem = new ShopItem();
                $shopItem->shop = $shop;
                $shopItem->item = $item;


                return [
                    $shopItem
                ];
            }
        ]);
    }
}