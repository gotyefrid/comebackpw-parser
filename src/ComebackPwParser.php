<?php

namespace Gotyefrid\ComebackpwParser;

use Gotyefrid\ComebackpwParser\Models\ShopItem;
use Gotyefrid\ComebackpwParser\Services\Dom\DomService;
use Gotyefrid\ComebackpwParser\Services\Parser\ParserService;

/**
 * Фасад
 */
class ComebackPwParser
{
    /**
     * @return ShopItem[]
     */
    public function parse(): array
    {
        $service = new ParserService([
            'domFinder' => new DomService()
        ]);

        return $service->run();
    }
}